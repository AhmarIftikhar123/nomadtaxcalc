<?php

namespace App\Services\TaxCalculator;

use App\Models\Country;
use App\Models\Deduction;
use App\Models\TaxBracket;
use App\Models\TaxType;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\DB;

/**
 * Calculate taxes for a single country across multiple tax types.
 *
 * Handles standard progressive/flat taxes, custom user-defined taxes,
 * multi-state calculations, deductions, and cross-currency bracket conversion.
 */
class TaxCalculationService
{
    public function __construct(
        protected CurrencyService $currencyService,
    ) {}

    /**
     * Calculate tax for a single country with multiple tax types.
     *
     * @param string|null $userCurrency  The currency the user's income is denominated in.
     *                                    When provided, income is converted from $userCurrency
     *                                    to the bracket currency before lookup, then result
     *                                    is converted back.
     */
    public function calculateForCountry(
        Country $country,
        float $allocatedIncome,
        array $taxTypesConfig = [],
        int $taxYear = 2026,
        ?int $stateId = null,
        ?string $userCurrency = null,
        ?string $filingStatus = null,
    ): array {
        $totalTax = 0;
        $breakdown = [];

        // If no config provided, default to Income Tax only
        if (empty($taxTypesConfig)) {
            $incomeTaxType = TaxType::where('key', 'income_tax')->first();
            if ($incomeTaxType) {
                $taxTypesConfig[] = ['tax_type_id' => $incomeTaxType->id, 'is_custom' => false];
            }
        }

        // ── Apply deductions to get taxable income ──────────────────────────
        $deductionAmount = $this->calculateDeductions($country, $allocatedIncome, $taxYear, $filingStatus);
        $taxableIncome   = max(0, $allocatedIncome - $deductionAmount);

        foreach ($taxTypesConfig as $config) {
            $taxAmount = 0;
            $details = '';
            $name = '';

            // BUG-4 FIX: Initialise $result so the standard breakdown push
            // on line ~149 always has bracket_details available, even when
            // the last config item is a custom tax that never sets $result.
            $result = ['bracket_details' => []];

            // Handle Custom Taxes
            if (!empty($config['is_custom'])) {
                $name = $config['custom_name'] ?? 'Custom Tax';
                $amountType = $config['amount_type'] ?? 'flat';
                $amountValue = (float) ($config['amount'] ?? 0);

                if ($amountType === 'percentage') {
                    $taxAmount = $taxableIncome * ($amountValue / 100);
                    $details = "{$amountValue}% of income";
                } else {
                    $taxAmount = $amountValue; // Flat amount
                    $details = "Flat annual amount";
                }

                $breakdown[] = [
                    'name' => $name,
                    'amount' => round($taxAmount, 2),
                    'details' => $details,
                    'is_custom' => true,
                    'rate' => $amountValue,
                    'type' => $amountType,
                ];
            }
            // Handle Standard Taxes (Income Tax, Social Security, etc.)
            else {
                $taxTypeId = $config['tax_type_id'];
                $taxType = TaxType::find($taxTypeId);

                if (!$taxType) continue;

                $name = $taxType->name;

                // CRITICAL FIX: Check if user provided a custom override amount
                if (isset($config['amount']) && $config['amount'] !== null && $config['amount'] !== '') {
                    // User provided custom rate/amount - use it instead of brackets
                    $amountType = $config['amount_type'] ?? 'percentage';
                    $amountValue = (float) $config['amount'];

                    if ($amountType === 'percentage') {
                        $taxAmount = $taxableIncome * ($amountValue / 100);
                        $details = "Custom rate: {$amountValue}%";
                    } else {
                        $taxAmount = $amountValue;
                        $details = "Custom flat amount";
                    }
                }
                // No override - use system brackets
                else {
                    // Special handling for Income Tax in Flat Tax Countries
                    if ($taxType->key === 'income_tax' && !$country->has_progressive_tax) {
                        $result = $this->calculateFlat($country, $taxableIncome);
                        $taxAmount = $result['tax_due'];
                        $details = "Flat rate: {$country->flat_tax_rate}%";
                    } else {
                        // Use Brackets (Progressive Income Tax, Social Security, etc.)
                        $result = $this->calculateBrackets($country, $taxableIncome, $taxTypeId, $taxYear, null, $userCurrency);
                        $taxAmount = $result['tax_due'];

                        if ($result['brackets_applied'] > 0) {
                            $details = "Progressive brackets ({$result['brackets_applied']} applied)";
                        } else {
                            // No brackets found - skip this tax type
                            continue;
                        }

                        // State Tax Handling
                        if ($stateId && $taxType->key === 'income_tax') {
                            $stateResult = $this->calculateBrackets($country, $taxableIncome, $taxTypeId, $taxYear, $stateId, $userCurrency);
                            if ($stateResult['brackets_applied'] > 0) {
                                $stateName = \App\Models\State::find($stateId)->name ?? 'State';
                                $stateTaxAmount = $stateResult['tax_due'];
                                $totalTax += $stateTaxAmount;

                                $breakdown[] = [
                                    'name' => "{$stateName} {$name}",
                                    'amount' => round($stateTaxAmount, 2),
                                    'details' => "Progressive brackets ({$stateResult['brackets_applied']} applied)",
                                    'is_custom' => false,
                                    'tax_type_key' => 'state_income_tax',
                                    'bracket_details' => $stateResult['bracket_details'] ?? [],
                                ];

                                $name = "Federal {$name}";
                            }
                        }
                    }
                }

                $breakdown[] = [
                    'name' => $name,
                    'amount' => round($taxAmount, 2),
                    'details' => $details,
                    'is_custom' => $config['is_custom'] ?? false,
                    'tax_type_key' => $taxType->key,
                    'bracket_details' => $result['bracket_details'] ?? [],
                ];
            }

            $totalTax += $taxAmount;
        }

        $effectiveRate = $allocatedIncome > 0 ? ($totalTax / $allocatedIncome) * 100 : 0;

        return [
            'taxable_income'    => $taxableIncome,
            'gross_income'      => $allocatedIncome,
            'deduction_amount'  => round($deductionAmount, 2),
            'tax_due'           => round($totalTax, 2),
            'effective_rate'    => round($effectiveRate, 2),
            'breakdown'         => $breakdown,
        ];
    }

    /**
     * Calculate tax using brackets (Generic for any tax type).
     *
     * When $userCurrency differs from the bracket's currency_code, income is
     * converted to the bracket currency before comparison, and the resulting
     * tax is converted back to $userCurrency.
     */
    private function calculateBrackets(
        Country $country,
        float $income,
        int $taxTypeId,
        int $taxYear = 2026,
        ?int $stateId = null,
        ?string $userCurrency = null,
    ): array {
        $brackets = TaxBracket::where('country_id', $country->id)
            ->where('state_id', $stateId)
            ->where('tax_type_id', $taxTypeId)
            ->where('tax_year', $taxYear)
            ->where('is_active', true)
            ->orderBy('min_income')
            ->get();

        if ($brackets->isEmpty()) {
            return ['tax_due' => 0, 'brackets_applied' => 0, 'bracket_details' => []];
        }

        // ── Currency conversion ──────────────────────────────────────────────
        // Determine bracket currency from the first bracket (all brackets for
        // a country share the same currency).
        $bracketCurrency = $brackets->first()->currency_code ?? $country->currency_code;
        $needsConversion = $userCurrency && $bracketCurrency && $userCurrency !== $bracketCurrency;
        $incomeInBracketCurrency = $needsConversion
            ? $this->currencyService->convert($income, $userCurrency, $bracketCurrency)
            : $income;

        $totalTax = 0;
        $bracketsApplied = 0;
        $bracketDetails = [];

        foreach ($brackets as $bracket) {
            $bracketMin = (float) $bracket->min_income;
            $bracketMax = $bracket->max_income ? (float) $bracket->max_income : PHP_FLOAT_MAX;
            $rate = (float) $bracket->rate / 100;

            // Income doesn't reach this bracket
            if ($incomeInBracketCurrency <= $bracketMin) {
                break;
            }

            // Calculate taxable amount in this bracket
            $taxableInBracket = min($incomeInBracketCurrency, $bracketMax) - $bracketMin;

            if ($taxableInBracket > 0) {
                $taxInBracket = $taxableInBracket * $rate;
                $totalTax += $taxInBracket;
                $bracketsApplied++;

                // If bracket has a cap, apply it
                if ($bracket->has_cap && $bracket->annual_cap) {
                    $cappedTax = min($totalTax, (float) $bracket->annual_cap);
                    $reduction = $totalTax - $cappedTax;
                    // Only reduce the current bracket's display if there's a real reduction
                    if ($reduction > 0) {
                        $taxInBracket = max(0, $taxInBracket - $reduction);
                    }
                    $totalTax = $cappedTax;
                }

                $bracketDetails[] = [
                    'min_income' => $bracketMin,
                    'max_income' => $bracketMax === PHP_FLOAT_MAX ? null : $bracketMax,
                    'rate' => $rate * 100,
                    'taxable_amount' => $taxableInBracket,
                    'tax_applied' => $taxInBracket,
                    'currency' => $bracketCurrency,
                ];
            }

            // Stop if we've exceeded the bracket's max
            if ($incomeInBracketCurrency <= $bracketMax) {
                break;
            }
        }

        // ── Convert tax back to user currency ────────────────────────────────
        if ($needsConversion && $totalTax > 0) {
            $totalTax = $this->currencyService->convert($totalTax, $bracketCurrency, $userCurrency);
        }

        return [
            'tax_due' => $totalTax,
            'brackets_applied' => $bracketsApplied,
            'bracket_details' => $bracketDetails,
        ];
    }

    /**
     * Calculate flat tax for countries with a single flat rate.
     *
     * @param  Country  $country  Country with a flat_tax_rate value.
     * @param  float    $income   Taxable income after deductions.
     * @return array{tax_due: float}
     */
    private function calculateFlat(Country $country, float $income): array
    {
        $rate = (float) $country->flat_tax_rate / 100;
        $taxDue = $income * $rate;

        return [
            'tax_due' => $taxDue,
        ];
    }

    /**
     * Allocate annual income to a country based on days spent or tax basis.
     *
     * Uses the country's `worldwide_income_threshold` to decide whether to
     * apply full worldwide income or days-based apportionment. Territorial
     * and remittance countries use only the locally-sourced income.
     *
     * @param  Country     $country                The country to allocate for.
     * @param  float       $annualIncome            Total annual income.
     * @param  int         $daysSpent               Days spent in this country.
     * @param  float|null  $localOrRemittedIncome   Income earned locally (territorial/remittance).
     * @return float                                 Allocated income for this country.
     */
    public function allocateIncome(Country $country, float $annualIncome, int $daysSpent, ?float $localOrRemittedIncome = null): float
    {
        // For territorial or remittance tax, taxation is based purely on locally-sourced/remitted income
        if (in_array($country->tax_basis, ['territorial', 'remittance'])) {
            return $localOrRemittedIncome ?? 0;
        }

        // Check worldwide_income_threshold for full worldwide taxation
        $threshold = $country->worldwide_income_threshold;

        if ($threshold !== null) {
            // threshold == 0 means ALWAYS tax full worldwide income (e.g. US citizens)
            if ($threshold === 0 || $daysSpent >= $threshold) {
                return $annualIncome;
            }
        }

        // Standard days-based apportionment
        return ($annualIncome / 365) * $daysSpent;
    }

    /**
     * Calculate applicable deductions for a country, year, and filing status.
     *
     * Checks the `deductions` table first for specific rules, then falls
     * back to the country-level `standard_deduction` column.
     *
     * @param  Country      $country       The country to look up deductions for.
     * @param  float        $income        Gross allocated income.
     * @param  int          $taxYear       Tax year for deduction lookup.
     * @param  string|null  $filingStatus  Optional filing status filter.
     * @return float                        Total deduction amount.
     */
    private function calculateDeductions(Country $country, float $income, int $taxYear, ?string $filingStatus = null): float
    {
        // 1. Check the deductions table for specific rules
        $query = Deduction::where('country_id', $country->id)
            ->where('tax_year', $taxYear)
            ->where('is_active', true);

        if ($filingStatus) {
            $query->where(function ($q) use ($filingStatus) {
                $q->where('filing_status', $filingStatus)
                  ->orWhereNull('filing_status');
            });
        }

        $deductions = $query->get();

        if ($deductions->isNotEmpty()) {
            $total = 0;
            foreach ($deductions as $deduction) {
                $total += $deduction->getEffectiveAmount($income);
            }
            return $total;
        }

        // 2. Fallback to the country-level standard_deduction column
        if ($country->standard_deduction && $country->standard_deduction > 0) {
            return (float) $country->standard_deduction;
        }

        return 0;
    }
}