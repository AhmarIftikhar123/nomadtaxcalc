<?php

namespace App\Services\TaxCalculator;

use App\Models\Country;
use App\Models\TaxBracket;
use App\Models\TaxType;
use Illuminate\Support\Facades\DB;

class TaxCalculationService
{
    /**
     * Calculate tax for a single country with multiple tax types
     * 
     * FIXED: Now properly handles custom taxes with overrides
     */
    public function calculateForCountry(Country $country, float $allocatedIncome, array $taxTypesConfig = [], int $taxYear = 2026, ?int $stateId = null): array
    {
        $totalTax = 0;
        $breakdown = [];

        // If no config provided, default to Income Tax only
        if (empty($taxTypesConfig)) {
            $incomeTaxType = TaxType::where('key', 'income_tax')->first();
            if ($incomeTaxType) {
                $taxTypesConfig[] = ['tax_type_id' => $incomeTaxType->id, 'is_custom' => false];
            }
        }

        foreach ($taxTypesConfig as $config) {
            $taxAmount = 0;
            $details = '';
            $name = '';
            
            // Handle Custom Taxes
            if (!empty($config['is_custom'])) {
                $name = $config['custom_name'] ?? 'Custom Tax';
                $amountType = $config['amount_type'] ?? 'flat';
                $amountValue = (float) ($config['amount'] ?? 0);

                if ($amountType === 'percentage') {
                    $taxAmount = $allocatedIncome * ($amountValue / 100);
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
                        $taxAmount = $allocatedIncome * ($amountValue / 100);
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
                        $result = $this->calculateFlat($country, $allocatedIncome);
                        $taxAmount = $result['tax_due'];
                        $details = "Flat rate: {$country->flat_tax_rate}%";
                    } else {
                        // Use Brackets (Progressive Income Tax, Social Security, etc.)
                        $result = $this->calculateBrackets($country, $allocatedIncome, $taxTypeId, $taxYear, null);
                        $taxAmount = $result['tax_due'];
                        
                        if ($result['brackets_applied'] > 0) {
                            $details = "Progressive brackets ({$result['brackets_applied']} applied)";
                        } else {
                            // No brackets found - skip this tax type
                            continue;
                        }

                        // State Tax Handling
                        if ($stateId && $taxType->key === 'income_tax') {
                            $stateResult = $this->calculateBrackets($country, $allocatedIncome, $taxTypeId, $taxYear, $stateId);
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
            'taxable_income' => $allocatedIncome,
            'tax_due' => round($totalTax, 2),
            'effective_rate' => round($effectiveRate, 2),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate tax using brackets (Generic for any tax type)
     */
    private function calculateBrackets(Country $country, float $income, int $taxTypeId, int $taxYear = 2026, ?int $stateId = null): array
    {
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

        $totalTax = 0;
        $bracketsApplied = 0;
        $bracketDetails = [];

        foreach ($brackets as $bracket) {
            $bracketMin = (float) $bracket->min_income;
            $bracketMax = $bracket->max_income ? (float) $bracket->max_income : PHP_FLOAT_MAX;
            $rate = (float) $bracket->rate / 100;

            // Income doesn't reach this bracket
            if ($income <= $bracketMin) {
                break;
            }

            // Calculate taxable amount in this bracket
            $taxableInBracket = min($income, $bracketMax) - $bracketMin;
            
            if ($taxableInBracket > 0) {
                $taxInBracket = $taxableInBracket * $rate;
                $totalTax += $taxInBracket;
                $bracketsApplied++;
                
                // If bracket has a cap, apply it
                if ($bracket->has_cap && $bracket->annual_cap) {
                    $cappedTax = min($totalTax, (float) $bracket->annual_cap);
                    // Adjust for display if capped
                    $taxInBracket = $taxInBracket - ($totalTax - $cappedTax); 
                    $totalTax = $cappedTax;
                }

                $bracketDetails[] = [
                    'min_income' => $bracketMin,
                    'max_income' => $bracketMax === PHP_FLOAT_MAX ? null : $bracketMax,
                    'rate' => $rate * 100,
                    'taxable_amount' => $taxableInBracket,
                    'tax_applied' => $taxInBracket
                ];
            }

            // Stop if we've exceeded the bracket's max
            if ($income <= $bracketMax) {
                break;
            }
        }

        return [
            'tax_due' => $totalTax,
            'brackets_applied' => $bracketsApplied,
            'bracket_details' => $bracketDetails
        ];
    }

    /**
     * Calculate flat tax (for countries with flat tax rate)
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
     * Allocate annual income based on days spent or tax basis
     */
    public function allocateIncome(Country $country, float $annualIncome, int $daysSpent, ?float $localOrRemittedIncome = null): float
    {
        // For territorial or remittance tax, taxation is based purely on locally-sourced/remitted income
        if (in_array($country->tax_basis, ['territorial', 'remittance'])) {
            return $localOrRemittedIncome ?? 0;
        }

        // Standard worldwide/progressive days-based apportionment
        return ($annualIncome / 365) * $daysSpent;
    }
}