<?php

namespace App\Services\TaxCalculator;

use App\Models\Country;
use App\Models\TaxBracket;
use Illuminate\Support\Facades\DB;

class TaxCalculationService
{
    /**
     * Calculate tax for a single country
     */
    public function calculateForCountry(Country $country, float $allocatedIncome): array
    {
        if ($country->has_progressive_tax) {
            return $this->calculateProgressive($country, $allocatedIncome);
        }

        return $this->calculateFlat($country, $allocatedIncome);
    }

    /**
     * Calculate progressive tax using brackets
     */
    private function calculateProgressive(Country $country, float $income): array
    {
        // Get the income_tax type ID to filter brackets correctly
        $incomeTaxTypeId = DB::table('tax_types')->where('key', 'income_tax')->value('id');

        $brackets = TaxBracket::where('country_id', $country->id)
            ->where('tax_type_id', $incomeTaxTypeId)
            ->where('tax_year', 2026)
            ->where('is_active', true)
            ->orderBy('min_income')
            ->get();

        if ($brackets->isEmpty()) {
            // Fallback if no brackets defined
            return [
                'taxable_income' => $income,
                'tax_due' => 0,
                'effective_rate' => 0,
                'method' => 'progressive',
                'details' => 'No tax brackets defined for this country',
            ];
        }

        $totalTax = 0;
        $previousMax = 0;

        foreach ($brackets as $bracket) {
            $bracketMin = (float) $bracket->min_income;
            $bracketMax = $bracket->max_income ? (float) $bracket->max_income : PHP_FLOAT_MAX;
            $rate = (float) $bracket->rate / 100;

            if ($income <= $bracketMin) {
                break;
            }

            $taxableInBracket = min($income, $bracketMax) - $bracketMin;
            if ($taxableInBracket > 0) {
                $totalTax += $taxableInBracket * $rate;
            }

            if ($income <= $bracketMax) {
                break;
            }
        }

        $effectiveRate = $income > 0 ? ($totalTax / $income) * 100 : 0;

        return [
            'taxable_income' => $income,
            'tax_due' => round($totalTax, 2),
            'effective_rate' => round($effectiveRate, 2),
            'method' => 'progressive',
            'brackets_applied' => $brackets->count(),
        ];
    }

    /**
     * Calculate flat tax
     */
    private function calculateFlat(Country $country, float $income): array
    {
        $rate = (float) $country->flat_tax_rate / 100;
        $taxDue = $income * $rate;
        $effectiveRate = (float) $country->flat_tax_rate;

        return [
            'taxable_income' => $income,
            'tax_due' => round($taxDue, 2),
            'effective_rate' => round($effectiveRate, 2),
            'method' => 'flat',
        ];
    }

    /**
     * Allocate annual income based on days spent
     */
    public function allocateIncome(float $annualIncome, int $daysSpent): float
    {
        return ($annualIncome / 365) * $daysSpent;
    }
}
