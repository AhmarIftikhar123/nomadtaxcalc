<?php

namespace App\Services\TaxCalculator;

use App\Models\Country;
use App\Models\UserCalculation;
use App\Models\UserCalculationCountry;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TaxCalculatorService
{
    public function __construct(
        protected ResidencyDeterminationService $residencyService,
        protected TaxCalculationService $taxCalcService,
        protected TreatyResolutionService $treatyService,
        protected FeieCalculationService $feieService,
        protected RecommendationService $recommendationService
    ) {}

    /**
     * Get list of active countries from database
     */
    public function getCountries()
    {
        return Country::active()
            ->select('id', 'name', 'iso_code', 'currency_code', 'currency_symbol')
            ->orderBy('name')
            ->get()
            ->map(function ($country) {
                return [
                    'id' => $country->id,
                    'name' => $country->name,
                    'code' => $country->iso_code,
                    'currency_code' => $country->currency_code,
                    'currency_symbol' => $country->currency_symbol,
                ];
            });
    }

    /**
     * Get list of supported currencies (dynamically from active countries)
     */
    public function getCurrencies(): array
    {
        return Country::active()
            ->select('currency_code', 'currency_symbol')
            ->distinct()
            ->orderBy('currency_code')
            ->get()
            ->map(fn($c) => [
                'code'   => $c->currency_code,
                'name'   => $c->currency_code . ' (' . $c->currency_symbol . ')',
                'symbol' => $c->currency_symbol,
            ])
            ->values()
            ->toArray();
    }

    /**
     * Save Step 1 data and create/update calculation
     */
    public function saveStep1Data(array $data, ?string $sessionUuid = null): UserCalculation
    {
        $country = Country::findOrFail($data['citizenship_country_id']);

        if (!$sessionUuid) {
            $sessionUuid = (string) Str::uuid();
        }

        $calculation = UserCalculation::updateOrCreate(
            ['session_uuid' => $sessionUuid],
            [
                'country_id' => $country->id,
                'domicile_state_id' => $data['domicile_state_id'] ?? null,
                'gross_income' => $data['annual_income'],
                'currency' => $data['currency'],
                'tax_year' => $data['tax_year'] ?? 2026,
                'citizenship_country_code' => $country->iso_code,
                'ip_address' => request()->ip(),
                'device_type' => $this->detectDeviceType(),
                'referrer' => request()->headers->get('referer'),
                'step_reached' => 1,
                'started_at' => now(),
            ]
        );

        return $calculation;
    }

    /**
     * Save Step 2 data (countries visited) with residency determination
     *
     * Reads `selected_tax_types` from the frontend payload (same key
     * used in the React form state) and processes them into DB columns.
     */
    public function saveStep2Data(UserCalculation $calculation, array $countriesVisited): void
    {
        // Delete existing countries
        $calculation->countriesVisited()->delete();

        // Determine tax residency for each country
        $residencyResults = $this->residencyService->determine($countriesVisited);

        // Create new country records with residency status AND custom taxes
        foreach ($residencyResults as $index => $result) {
            $originalVisit = $countriesVisited[$index] ?? null;

            $countryData = [
                'user_calculation_id' => $calculation->id,
                'country_id' => $result['country_id'],
                'state_id' => $originalVisit['state_id'] ?? null,
                'days_spent' => $result['days_spent'],
                'is_tax_resident' => $result['is_tax_resident'],
            ];

            // Read selected_tax_types from frontend (matches React form key)
            $taxTypes = $originalVisit['selected_tax_types'] ?? [];

            if (!empty($taxTypes)) {
                $processedTaxes = $this->processCustomTaxes($taxTypes);
                $countryData['selected_tax_type_ids'] = $processedTaxes['selected_tax_type_ids'];
                $countryData['tax_type_overrides'] = $processedTaxes['tax_type_overrides'];
            } else {
                // Default: only income tax
                $countryData['selected_tax_type_ids'] = [1];
                $countryData['tax_type_overrides'] = null;
            }

            UserCalculationCountry::create($countryData);
        }
    }

    /**
     * NEW METHOD: Process custom taxes from frontend format to backend format
     */
    private function processCustomTaxes(array $customTaxes): array
    {
        $selectedTaxTypeIds = [1]; // Always include income_tax (id: 1)
        $taxTypeOverrides = [];

        foreach ($customTaxes as $tax) {
            // Skip if no amount provided
            if (!isset($tax['amount']) || $tax['amount'] === '' || $tax['amount'] === null) {
                continue;
            }

            if ($tax['is_custom'] ?? false) {
                // Custom tax type - generate unique ID
                $customId = 'custom_' . uniqid();
                $selectedTaxTypeIds[] = $customId;

                $taxTypeOverrides[$customId] = [
                    'name' => $tax['custom_name'] ?? 'Custom Tax',
                    'type' => $tax['amount_type'] ?? 'percentage',
                    'amount' => (float) $tax['amount'],
                    'is_custom' => true,
                ];
            } else {
                // Predefined tax type
                if (!isset($tax['tax_type_id']) || empty($tax['tax_type_id'])) {
                    continue;
                }

                $taxTypeId = (int) $tax['tax_type_id'];
                $selectedTaxTypeIds[] = $taxTypeId;

                // Store override amount
                $taxTypeOverrides[$taxTypeId] = [
                    'type' => $tax['amount_type'] ?? 'percentage',
                    'amount' => (float) $tax['amount'],
                    'is_custom' => false,
                ];
            }
        }

        return [
            'selected_tax_type_ids' => array_unique($selectedTaxTypeIds),
            'tax_type_overrides' => !empty($taxTypeOverrides) ? $taxTypeOverrides : null,
        ];
    }

    /**
     * Calculate taxes (orchestrates all services)
     * 
     * FIXED: Now properly passes custom tax configuration to TaxCalculationService
     */
    public function calculateTaxes(UserCalculation $calculation): array
    {
        $countriesVisited = $calculation->countriesVisited()->with('country')->get();
        $annualIncome = $calculation->gross_income;
        $taxYear = $calculation->tax_year ?? Carbon::now()->year;
        $citizenshipCountryId = $calculation->country_id;

        // Step 1: Determine residency (already done in Step 2, retrieve results)
        $residencyResults = [];
        $taxResidentCountries = [];

        foreach ($countriesVisited as $visitedCountry) {
            $country = $visitedCountry->country;
            $hasIncomeTax = $country->has_progressive_tax || ($country->flat_tax_rate !== null && $country->flat_tax_rate > 0);

            $result = [
                'country_id' => $visitedCountry->country_id,
                'country_name' => $country->name,
                'country_code' => $country->iso_code,
                'days_spent' => $visitedCountry->days_spent,
                'is_tax_resident' => $visitedCountry->is_tax_resident,
                'threshold' => $country->tax_residency_days,
                'has_income_tax' => $hasIncomeTax,
            ];

            $residencyResults[] = $result;

            if ($visitedCountry->is_tax_resident) {
                $taxResidentCountries[] = $visitedCountry;
            }
        }
        // Step 2: Calculate tax for each tax-resident country
        $countryBreakdown = [];
        // dd($countriesVisited);
        foreach ($taxResidentCountries as $residentCountry) {
            $country = $residentCountry->country;
            $allocatedIncome = $this->taxCalcService->allocateIncome($country, $annualIncome, $residentCountry->days_spent);

            // CRITICAL FIX: Build proper tax types config from stored data
            $taxTypesConfig = $this->buildTaxTypesConfig($residentCountry);

            // Calculate tax with custom taxes included
            $taxResult = $this->taxCalcService->calculateForCountry(
                $country,
                $allocatedIncome,
                $taxTypesConfig,
                $taxYear,
                $residentCountry->state_id
            );

            $breakdown = [
                'country_id' => $country->id,
                'country_name' => $country->name,
                'country_code' => $country->iso_code,
                'currency' => $calculation->currency,
                'days_spent' => $residentCountry->days_spent,
                'allocated_income' => round($allocatedIncome, 2),
                'taxable_income' => round($taxResult['taxable_income'], 2),
                'tax_due' => $taxResult['tax_due'],
                'effective_rate' => round($taxResult['effective_rate'], 2),
                'tax_type_breakdown' => $taxResult['breakdown'], // Detailed breakdown including custom taxes
                'method' => 'aggregated',
                'is_tax_resident' => true,
                'threshold' => $country->tax_residency_days,
            ];

            $countryBreakdown[] = $breakdown;

            // Update database
            $residentCountry->update([
                'allocated_income' => $allocatedIncome,
                'taxable_income' => $taxResult['taxable_income'],
                'tax_due' => $taxResult['tax_due'],
                'tax_by_type' => $taxResult['breakdown'],
            ]);
        }
        // Step 3: Apply treaties to prevent double taxation
        $treatyApplicationResult = $this->treatyService->applyTreaty($citizenshipCountryId, $countryBreakdown, $taxYear);
        $countryBreakdown = $treatyApplicationResult['results'];
        $treatiesApplied = $treatyApplicationResult['treaties_applied'];
        // Step 4: Check FEIE for US citizens
        $feieResult = $this->feieService->calculate($citizenshipCountryId, $residencyResults, $annualIncome, $taxYear);

        // If US citizen and FEIE eligible, adjust US tax
        if ($feieResult && $feieResult['eligible']) {
            foreach ($countryBreakdown as &$breakdown) {
                $country = Country::find($breakdown['country_id']);
                if ($country && $country->iso_code === 'US') {
                    $adjustedIncome = max(0, $breakdown['taxable_income'] - $feieResult['excluded_income']);

                    // Rebuild tax types config for US
                    $usCountry = $countriesVisited->firstWhere('country_id', $breakdown['country_id']);
                    $taxTypesConfig = $usCountry ? $this->buildTaxTypesConfig($usCountry) : [];

                    // State ID from visit or domicile
                    $stateId = $usCountry ? $usCountry->state_id : $calculation->domicile_state_id;

                    $adjustedTax = $this->taxCalcService->calculateForCountry($country, $adjustedIncome, $taxTypesConfig, $taxYear, $stateId);
                    $breakdown['tax_due'] = $adjustedTax['tax_due'];
                    $breakdown['feie_applied'] = true;
                    $breakdown['feie_exclusion'] = $feieResult['excluded_income'];
                }
            }
        }

        // Step 5: Aggregate totals
        $totalTax = array_sum(array_column($countryBreakdown, 'tax_due'));
        $netIncome = $annualIncome - $totalTax;
        $effectiveTaxRate = $annualIncome > 0 ? ($totalTax / $annualIncome) * 100 : 0;

        // Step 6: Generate recommendations
        $recommendations = $this->recommendationService->generate($residencyResults, $countryBreakdown, $totalTax);

        // Step 7: Generate residency warnings
        $residencyWarnings = $this->residencyService->generateWarnings($residencyResults);

        // Step 8: Update calculation with final results
        $calculation->update([
            'taxable_income' => $annualIncome,
            'total_tax' => $totalTax,
            'net_income' => $netIncome,
            'effective_tax_rate' => $effectiveTaxRate,
            'step_reached' => 3,
            'completed_calculation' => true,
            'completed_at' => now(),
            'tax_breakdown' => $countryBreakdown,
            'residency_warnings' => $residencyWarnings,
            'treaty_applied' => $treatiesApplied,
            'feie_result' => $feieResult,
        ]);

        return [
            'annual_income' => round($annualIncome, 2),
            'currency' => $calculation->currency,
            'tax_year' => $taxYear,
            'total_tax' => round($totalTax, 2),
            'net_income' => round($netIncome, 2),
            'effective_tax_rate' => round($effectiveTaxRate, 2),
            'breakdown_by_country' => $countryBreakdown,
            'residency_warnings' => $residencyWarnings,
            'residency_data' => $residencyResults,
            'comparison_data' => $this->generateComparisonData($countryBreakdown),
            'treaties_applied' => $treatiesApplied,
            'feie_result' => $feieResult,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * NEW METHOD: Build tax types config from stored UserCalculationCountry data
     * This converts database format to the format expected by TaxCalculationService
     */
    private function buildTaxTypesConfig(UserCalculationCountry $calculationCountry): array
    {
        $config = [];

        $selectedTaxTypeIds = $calculationCountry->selected_tax_type_ids ?? [1];
        $overrides = $calculationCountry->tax_type_overrides ?? [];

        foreach ($selectedTaxTypeIds as $taxTypeId) {
            // Check if this is a custom tax (string starting with "custom_")
            if (is_string($taxTypeId) && str_starts_with($taxTypeId, 'custom_')) {
                $override = $overrides[$taxTypeId] ?? null;
                if ($override) {
                    $config[] = [
                        'is_custom' => true,
                        'custom_name' => $override['name'] ?? 'Custom Tax',
                        'amount_type' => $override['type'] ?? 'percentage',
                        'amount' => $override['amount'] ?? 0,
                    ];
                }
            } else {
                // Standard tax type
                $taxConfig = [
                    'tax_type_id' => (int) $taxTypeId,
                    'is_custom' => false,
                ];

                // Add override if exists
                if (isset($overrides[$taxTypeId])) {
                    $taxConfig['amount_type'] = $overrides[$taxTypeId]['type'] ?? 'percentage';
                    $taxConfig['amount'] = $overrides[$taxTypeId]['amount'] ?? null;
                }

                $config[] = $taxConfig;
            }
        }

        return $config;
    }

    /**
     * Detect device type from user agent
     */
    private function detectDeviceType(): string
    {
        $userAgent = request()->header('User-Agent');

        if (preg_match('/mobile/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Generate comparison data for visualization components
     */
    private function generateComparisonData(array $countryBreakdown): array
    {
        $comparison = [];

        foreach ($countryBreakdown as $breakdown) {
            $comparison[] = [
                'country' => $breakdown['country_name'],
                'liability' => $breakdown['tax_due'],
                'percentage' => $breakdown['effective_rate'],
            ];
        }

        // Sort by tax amount descending
        usort($comparison, fn($a, $b) => $b['liability'] <=> $a['liability']);

        return $comparison;
    }
}
