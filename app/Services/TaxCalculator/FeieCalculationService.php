<?php

namespace App\Services\TaxCalculator;

use App\Models\Setting;
use App\Models\Country;

class FeieCalculationService
{
    /**
     * Calculate FEIE (Foreign Earned Income Exclusion) for US citizens
     */
    public function calculate(int $citizenshipCountryId, array $residencyResults, float $annualIncome): ?array
    {
        // Check if US citizen
        $usaCountry = Country::where('iso_code', 'US')->first();
        if (!$usaCountry || $citizenshipCountryId !== $usaCountry->id) {
            return null; // Not applicable
        }

        // Count days spent outside US
        $daysOutsideUs = 0;
        foreach ($residencyResults as $result) {
            if ($result['country_id'] !== $usaCountry->id) {
                $daysOutsideUs += $result['days_spent'];
            }
        }

        // Get FEIE settings
        $feieLimit = (float) Setting::get('feie_amount_2026', 126500);
        $minDays = (int) Setting::get('feie_min_days', 330);

        $eligible = $daysOutsideUs >= $minDays;
        $excludedIncome = 0;
        $reason = '';

        if ($eligible) {
            $excludedIncome = min($annualIncome, $feieLimit);
            $reason = "Qualified under Physical Presence Test ({$daysOutsideUs} days outside US exceeds {$minDays} days)";
        } else {
            $daysDiff = $minDays - $daysOutsideUs;
            $reason = "Not qualified - spent only {$daysOutsideUs} days outside US, need {$daysDiff} more days to reach {$minDays}";
        }

        return [
            'eligible' => $eligible,
            'days_outside_us' => $daysOutsideUs,
            'minimum_required' => $minDays,
            'feie_limit' => $feieLimit,
            'excluded_income' => $excludedIncome,
            'taxable_us_income' => max(0, $annualIncome - $excludedIncome),
            'reason' => $reason,
        ];
    }
}
