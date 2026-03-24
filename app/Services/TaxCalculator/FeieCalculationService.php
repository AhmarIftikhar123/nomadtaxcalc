<?php

namespace App\Services\TaxCalculator;

use App\Models\Setting;
use App\Models\Country;

/**
 * Calculate the Foreign Earned Income Exclusion (FEIE) for US citizens.
 *
 * FEIE allows qualifying US citizens/residents abroad to exclude a portion
 * of foreign earned income from federal income tax. Does NOT affect
 * self-employment tax (SS/FICA) — that is always on full income.
 */
class FeieCalculationService
{
    /**
     * Determine FEIE eligibility and calculate the exclusion amount.
     *
     * Eligibility requires the user to spend at least 330 full days outside
     * the US in a 12-month period (Physical Presence Test). FEIE only
     * reduces federal income tax — self-employment tax (SS/FICA) is
     * always calculated on full pre-FEIE income per IRS rules.
     *
     * @param  int     $citizenshipCountryId  User's citizenship country ID.
     * @param  array   $residencyResults      Per-country residency results.
     * @param  float   $annualIncome          Total annual income.
     * @param  int     $taxYear               Tax year for FEIE limit lookup.
     * @return array|null                      Eligibility result, or null if not US citizen.
     */
    public function calculate(int $citizenshipCountryId, array $residencyResults, float $annualIncome, int $taxYear = 2026): ?array
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
        $feieLimit = (float) Setting::get("feie_amount_{$taxYear}", 126500);
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
