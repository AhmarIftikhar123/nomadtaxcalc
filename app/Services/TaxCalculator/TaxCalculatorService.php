<?php

namespace App\Services\TaxCalculator;

use App\Models\Country;
use App\Models\UserCalculation;
use App\Models\UserCalculationCountry;
use App\Services\CurrencyService;
use Carbon\Carbon;

class TaxCalculatorService
{
    public function __construct(
        protected ResidencyDeterminationService $residencyService,
        protected TaxCalculationService $taxCalcService,
        protected TreatyResolutionService $treatyService,
        protected FeieCalculationService $feieService,
        protected RecommendationService $recommendationService,
        protected CurrencyService $currencyService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Reference Data
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Active countries for dropdowns.
     * Includes tax_basis so the frontend can show the territorial income field.
     */
    public function getCountries()
    {
        return Country::active()
            ->select('id', 'name', 'iso_code', 'currency_code', 'currency_symbol', 'tax_basis')
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'id'              => $c->id,
                'name'            => $c->name,
                'code'            => $c->iso_code,
                'currency_code'   => $c->currency_code,
                'currency_symbol' => $c->currency_symbol,
                'tax_basis'       => $c->tax_basis,
            ]);
    }

    /**
     * Distinct currencies from active countries.
     */
    public function getCurrencies(): array
    {
        return Country::active()
            ->select('currency_code', 'currency_symbol')
            ->distinct()
            ->orderBy('currency_code')
            ->get()
            ->map(fn ($c) => [
                'code'   => $c->currency_code,
                'name'   => $c->currency_code . ' (' . $c->currency_symbol . ')',
                'symbol' => $c->currency_symbol,
            ])
            ->values()
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Session-Based Calculation Flow (Anonymous + Auth)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Validate & normalise Step 1 data for storage in session.
     * No DB write.
     */
    public function buildSessionStep1Payload(array $data): array
    {
        $country = Country::findOrFail($data['citizenship_country_id']);

        return [
            'citizenship_country_id'   => $country->id,
            'citizenship_country_code' => $country->iso_code,
            'citizenship_country_name' => $country->name,
            'annual_income'            => (float) $data['annual_income'],
            'currency'                 => $data['currency'],
            'tax_year'                 => (int) ($data['tax_year'] ?? Carbon::now()->year),
            'domicile_state_id'        => $data['domicile_state_id'] ?? null,
        ];
    }

    /**
     * Run the full tax pipeline from raw session arrays.
     * This is the core calculation method — called right after Step 2 submit.
     */
    public function calculateTaxesFromSession(array $step1, array $periods): array
    {
        $annualIncome          = (float) $step1['annual_income'];
        $taxYear               = (int) ($step1['tax_year'] ?? Carbon::now()->year);
        $citizenshipCountryId  = (int) $step1['citizenship_country_id'];
        $currency              = $step1['currency'];
        $domicileStateId       = $step1['domicile_state_id'] ?? null;

        // ── Step 1: Determine residency ──────────────────────────────────────
        $residencyResults  = $this->residencyService->determine($periods);
        $taxResidentPeriods = [];

        foreach ($residencyResults as $index => $result) {
            $country       = Country::find($result['country_id']);
            $hasIncomeTax  = $country && (
                $country->has_progressive_tax ||
                ($country->flat_tax_rate !== null && $country->flat_tax_rate > 0)
            );

            $residencyResults[$index]['country_name'] = $country?->name ?? '';
            $residencyResults[$index]['country_code'] = $country?->iso_code ?? '';
            $residencyResults[$index]['has_income_tax'] = $hasIncomeTax;
            $residencyResults[$index]['threshold'] = $country?->tax_residency_days;

            // Carry local_income from the original period payload
            $residencyResults[$index]['local_income'] = $periods[$index]['local_income'] ?? null;

            if ($result['is_tax_resident']) {
                $taxResidentPeriods[] = array_merge($result, [
                    'state_id'     => $periods[$index]['state_id'] ?? null,
                    'local_income' => $periods[$index]['local_income'] ?? null,
                    'selected_tax_types' => $periods[$index]['selected_tax_types'] ?? [],
                ]);
            }
        }

        // ── Step 2: Tax per resident country ────────────────────────────────
        $countryBreakdown = [];

        foreach ($taxResidentPeriods as $period) {
            $country = Country::find($period['country_id']);
            if (!$country) continue;

            $localIncomeRaw      = (isset($period['local_income']) && $period['local_income'] !== '' && $period['local_income'] !== null)
                ? (float) $period['local_income']
                : null;

            // ── Currency conversion for local income ──────────────────────
            $localIncomeCurrency = $period['local_income_currency'] ?? $currency;
            $localIncomeConverted = null;

            if ($localIncomeRaw !== null) {
                if ($localIncomeCurrency !== $currency) {
                    $localIncomeConverted = $this->currencyService->convert($localIncomeRaw, $localIncomeCurrency, $currency);
                } else {
                    $localIncomeConverted = $localIncomeRaw;
                }
            }

            $allocatedIncome = $this->taxCalcService->allocateIncome(
                $country,
                $annualIncome,
                $period['days_spent'],
                $localIncomeConverted // already in step1 currency
            );

            $taxTypesConfig = $this->buildTaxTypesConfigFromPeriod($period['selected_tax_types'] ?? []);

            $taxResult = $this->taxCalcService->calculateForCountry(
                $country,
                $allocatedIncome,
                $taxTypesConfig,
                $taxYear,
                $period['state_id'] ?? null
            );

            $countryBreakdown[] = [
                'country_id'                => $country->id,
                'country_name'              => $country->name,
                'country_code'              => $country->iso_code,
                'tax_basis'                 => $country->tax_basis,
                'currency'                  => $currency,
                'days_spent'                => $period['days_spent'],
                'local_income'              => $localIncomeConverted,     // in step1 currency
                'local_income_original'     => $localIncomeRaw,           // what user typed
                'local_income_currency'     => $localIncomeCurrency,      // currency user selected
                'allocated_income'          => round($allocatedIncome, 2),
                'taxable_income'            => round($taxResult['taxable_income'], 2),
                'tax_due'                   => $taxResult['tax_due'],
                'effective_rate'            => round($taxResult['effective_rate'], 2),
                'tax_type_breakdown'        => $taxResult['breakdown'],
                'method'                    => 'aggregated',
                'is_tax_resident'           => true,
                'threshold'                 => $country->tax_residency_days,
            ];
        }

        // ── Step 3: Treaties ────────────────────────────────────────────────
        $treatyApplicationResult = $this->treatyService->applyTreaty($citizenshipCountryId, $countryBreakdown, $taxYear);
        $countryBreakdown        = $treatyApplicationResult['results'];
        $treatiesApplied         = $treatyApplicationResult['treaties_applied'];

        // ── Step 4: FEIE (US citizens) ──────────────────────────────────────
        $feieResult = $this->feieService->calculate($citizenshipCountryId, $residencyResults, $annualIncome, $taxYear);

        if ($feieResult && $feieResult['eligible']) {
            foreach ($countryBreakdown as &$bd) {
                $c = Country::find($bd['country_id']);
                if ($c && $c->iso_code === 'US') {
                    $adjustedIncome = max(0, $bd['taxable_income'] - $feieResult['excluded_income']);
                    $period = collect($taxResidentPeriods)->firstWhere('country_id', $bd['country_id']);
                    $taxTypesConfig = $this->buildTaxTypesConfigFromPeriod($period['selected_tax_types'] ?? []);
                    $stateId = $period['state_id'] ?? $domicileStateId;
                    $adjustedTax = $this->taxCalcService->calculateForCountry($c, $adjustedIncome, $taxTypesConfig, $taxYear, $stateId);
                    $bd['tax_due']        = $adjustedTax['tax_due'];
                    $bd['feie_applied']   = true;
                    $bd['feie_exclusion'] = $feieResult['excluded_income'];
                }
            }
            unset($bd);
        }

        // ── Step 5: Aggregate ────────────────────────────────────────────────
        $totalTax        = array_sum(array_column($countryBreakdown, 'tax_due'));
        $netIncome       = $annualIncome - $totalTax;
        $effectiveTaxRate = $annualIncome > 0 ? ($totalTax / $annualIncome) * 100 : 0;

        // ── Step 6: Recommendations & Warnings ──────────────────────────────
        $recommendations  = $this->recommendationService->generate($residencyResults, $countryBreakdown, $totalTax);
        $residencyWarnings = $this->residencyService->generateWarnings($residencyResults);

        return [
            'annual_income'      => round($annualIncome, 2),
            'currency'           => $currency,
            'tax_year'           => $taxYear,
            'total_tax'          => round($totalTax, 2),
            'net_income'         => round($netIncome, 2),
            'effective_tax_rate' => round($effectiveTaxRate, 2),
            'breakdown_by_country' => $countryBreakdown,
            'residency_warnings' => $residencyWarnings,
            'residency_data'     => $residencyResults,
            'comparison_data'    => $this->generateComparisonData($countryBreakdown),
            'treaties_applied'   => $treatiesApplied,
            'feie_result'        => $feieResult,
            'recommendations'    => $recommendations,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Auth-Gated DB Save
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Persist a completed calculation for a logged-in user.
     * Called only when the user explicitly clicks "Save Calculation".
     *
     * @param  int|null  $calculationId  Pass existing ID to update, null to create.
     */
    public function saveCalculationForUser(int $userId, array $step1, array $periods, array $result, ?int $calculationId = null): UserCalculation
    {
        if (!$step1 || !$result) {
            throw new \InvalidArgumentException('Cannot save: missing step 1 or result data.');
        }

        $country = Country::findOrFail($step1['citizenship_country_id']);

        $attributes = [
            'user_id'                  => $userId,
            'country_id'               => $country->id,
            'citizenship_country_code' => $country->iso_code,
            'domicile_state_id'        => $step1['domicile_state_id'] ?? null,
            'gross_income'             => $step1['annual_income'],
            'currency'                 => $step1['currency'],
            'tax_year'                 => $step1['tax_year'],
            'ip_address'               => request()->ip(),
            'device_type'              => $this->detectDeviceType(),
            'referrer'                 => request()->headers->get('referer'),
            'step_reached'             => 3,
            'started_at'               => now(),
            'completed_at'             => now(),
            'completed_calculation'    => true,
            'taxable_income'           => $result['annual_income'],
            'total_tax'                => $result['total_tax'],
            'net_income'               => $result['net_income'],
            'effective_tax_rate'       => $result['effective_tax_rate'],
            'tax_breakdown'            => $result['breakdown_by_country'],
            'residency_warnings'       => $result['residency_warnings'],
            'treaty_applied'           => $result['treaties_applied'],
            'feie_result'              => $result['feie_result'],
        ];

        if ($calculationId) {
            $calculation = UserCalculation::where('id', $calculationId)
                ->where('user_id', $userId)
                ->firstOrFail();
            $calculation->update($attributes);
        } else {
            $calculation = UserCalculation::create($attributes);
        }

        // Re-save country periods
        $calculation->countriesVisited()->delete();

        $residencyResults = $this->residencyService->determine($periods);

        foreach ($residencyResults as $index => $residency) {
            $period   = $periods[$index];
            $taxTypes = $period['selected_tax_types'] ?? [];
            $processed = !empty($taxTypes) ? $this->processCustomTaxes($taxTypes) : [
                'selected_tax_type_ids' => [1],
                'tax_type_overrides'    => null,
            ];

            $localIncome = isset($period['local_income']) && $period['local_income'] !== ''
                ? (float) $period['local_income']
                : null;

            UserCalculationCountry::create([
                'user_calculation_id'   => $calculation->id,
                'country_id'            => $residency['country_id'],
                'state_id'              => $period['state_id'] ?? null,
                'days_spent'            => $residency['days_spent'],
                'local_income'          => $localIncome,
                'is_tax_resident'       => $residency['is_tax_resident'],
                'selected_tax_type_ids' => $processed['selected_tax_type_ids'],
                'tax_type_overrides'    => $processed['tax_type_overrides'],
            ]);
        }

        return $calculation;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Load from DB (query-string edit flow)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Rebuild the Step 1 + Step 2 prefill arrays from a saved UserCalculation.
     * Used when loading /tax-calculator?calculation_id=X.
     */
    public function rebuildPrefillFromCalculation(UserCalculation $calculation): array
    {
        $citizenshipCountry = Country::find($calculation->country_id);

        $step1 = [
            'annual_income'              => $calculation->gross_income,
            'currency'                   => $calculation->currency,
            'tax_year'                   => $calculation->tax_year,
            'citizenship_country_id'     => $citizenshipCountry?->id ?? '',
            'citizenship_country_name'   => $citizenshipCountry?->name ?? 'Unknown',
            'citizenship_country_code'   => $calculation->citizenship_country_code,
            'domicile_state_id'          => $calculation->domicile_state_id ?? '',
        ];

        $periods = [];

        if ($calculation->countriesVisited->isNotEmpty()) {
            $periods = $calculation->countriesVisited->map(function ($visit) {
                $selectedTaxTypes = [];
                $storedIds        = $visit->selected_tax_type_ids ?? [];
                $overrides        = $visit->tax_type_overrides ?? [];

                foreach ($storedIds as $taxTypeId) {
                    if (is_string($taxTypeId) && str_starts_with($taxTypeId, 'custom_')) {
                        $override = $overrides[$taxTypeId] ?? [];
                        $selectedTaxTypes[] = [
                            'id'           => crc32($taxTypeId),
                            'tax_type_id'  => '',
                            'custom_name'  => $override['name'] ?? 'Custom Tax',
                            'amount_type'  => $override['type'] ?? 'percentage',
                            'amount'       => $override['amount'] ?? '',
                            'is_custom'    => true,
                        ];
                    } else {
                        $override = $overrides[$taxTypeId] ?? $overrides[(string) $taxTypeId] ?? [];
                        $selectedTaxTypes[] = [
                            'id'          => (int) $taxTypeId * 1000 + $visit->id,
                            'tax_type_id' => (string) $taxTypeId,
                            'custom_name' => '',
                            'amount_type' => $override['type'] ?? 'percentage',
                            'amount'      => $override['amount'] ?? '',
                            'is_custom'   => false,
                        ];
                    }
                }

                return [
                    'id'                 => $visit->id,
                    'country_id'         => $visit->country_id,
                    'state_id'           => $visit->state_id ?? '',
                    'country_name'       => $visit->country?->name ?? '',
                    'country_code'       => $visit->country?->iso_code ?? '',
                    'country_tax_basis'  => $visit->country?->tax_basis ?? 'worldwide',
                    'days'               => $visit->days_spent,
                    'local_income'       => $visit->local_income ?? '',
                    'isTaxResident'      => (bool) $visit->is_tax_resident,
                    'selected_tax_types' => $selectedTaxTypes,
                ];
            })->toArray();
        }

        return compact('step1', 'periods');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build tax types config array from the raw frontend selected_tax_types array.
     */
    private function buildTaxTypesConfigFromPeriod(array $selectedTaxTypes): array
    {
        if (empty($selectedTaxTypes)) {
            return [];
        }

        $config = [];

        foreach ($selectedTaxTypes as $tax) {
            if ($tax['is_custom'] ?? false) {
                if (!isset($tax['amount']) || $tax['amount'] === '' || $tax['amount'] === null) continue;
                $config[] = [
                    'is_custom'   => true,
                    'custom_name' => $tax['custom_name'] ?? 'Custom Tax',
                    'amount_type' => $tax['amount_type'] ?? 'percentage',
                    'amount'      => (float) $tax['amount'],
                ];
            } else {
                if (empty($tax['tax_type_id'])) continue;
                $entry = ['tax_type_id' => (int) $tax['tax_type_id'], 'is_custom' => false];
                if (isset($tax['amount']) && $tax['amount'] !== '' && $tax['amount'] !== null) {
                    $entry['amount_type'] = $tax['amount_type'] ?? 'percentage';
                    $entry['amount']      = (float) $tax['amount'];
                }
                $config[] = $entry;
            }
        }

        return $config;
    }

    /**
     * Process custom taxes from frontend format to DB storage format.
     */
    private function processCustomTaxes(array $customTaxes): array
    {
        $selectedTaxTypeIds = [1]; // Always include income_tax (id: 1)
        $taxTypeOverrides   = [];

        foreach ($customTaxes as $tax) {
            if (!isset($tax['amount']) || $tax['amount'] === '' || $tax['amount'] === null) continue;

            if ($tax['is_custom'] ?? false) {
                $customId = 'custom_' . uniqid();
                $selectedTaxTypeIds[] = $customId;
                $taxTypeOverrides[$customId] = [
                    'name'      => $tax['custom_name'] ?? 'Custom Tax',
                    'type'      => $tax['amount_type'] ?? 'percentage',
                    'amount'    => (float) $tax['amount'],
                    'is_custom' => true,
                ];
            } else {
                if (!isset($tax['tax_type_id']) || empty($tax['tax_type_id'])) continue;
                $taxTypeId = (int) $tax['tax_type_id'];
                $selectedTaxTypeIds[] = $taxTypeId;
                $taxTypeOverrides[$taxTypeId] = [
                    'type'      => $tax['amount_type'] ?? 'percentage',
                    'amount'    => (float) $tax['amount'],
                    'is_custom' => false,
                ];
            }
        }

        return [
            'selected_tax_type_ids' => array_unique($selectedTaxTypeIds),
            'tax_type_overrides'    => !empty($taxTypeOverrides) ? $taxTypeOverrides : null,
        ];
    }

    private function detectDeviceType(): string
    {
        $ua = request()->header('User-Agent');
        if (preg_match('/mobile/i', $ua)) return 'mobile';
        if (preg_match('/tablet/i', $ua)) return 'tablet';
        return 'desktop';
    }

    private function generateComparisonData(array $countryBreakdown): array
    {
        $comparison = array_map(fn ($bd) => [
            'country'    => $bd['country_name'],
            'liability'  => $bd['tax_due'],
            'percentage' => $bd['effective_rate'],
        ], $countryBreakdown);

        usort($comparison, fn ($a, $b) => $b['liability'] <=> $a['liability']);

        return $comparison;
    }
}
