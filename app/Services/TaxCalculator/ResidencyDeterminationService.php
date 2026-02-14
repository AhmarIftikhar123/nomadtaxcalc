<?php

namespace App\Services\TaxCalculator;

use App\Models\Country;
use App\Models\UserCalculationCountry;

class ResidencyDeterminationService
{
    /**
     * Determine tax residency status for each visited country
     */
    public function determine(array $visitedCountries): array
    {
        $results = [];
        // dd($visitedCountries);
        foreach ($visitedCountries as $visit) {
            $country = Country::find($visit['country_id']);
            if (!$country) {
                continue;
            }
            
            $daysSpent = $visit['days'];
            $threshold = $country->tax_residency_days;

            // Apply arrival/departure day rules
            if (!$country->counts_arrival_day) {
                $daysSpent -= 1;
            }
            if (!$country->counts_departure_day) {
                $daysSpent -= 1;
            }

            // Determine tax residency
            $isResident = $daysSpent >= $threshold;

            $results[] = [
                'country_id' => $country->id,
                'country_name' => $country->name,
                'days_spent' => $visit['days'],
                'threshold' => $threshold,
                'is_tax_resident' => $isResident,
                'reason' => $this->getResidencyReason($daysSpent, $threshold, $isResident),
            ];
        }

        return $results;
    }

    /**
     * Get human-readable reason for residency determination
     */
    private function getResidencyReason(int $days, int $threshold, bool $isResident): string
    {
        if ($isResident) {
            return "Spent {$days} days, exceeding the {$threshold}-day threshold.";
        }

        $remaining = $threshold - $days;
        return "Spent {$days} days, below the {$threshold}-day threshold by {$remaining} days.";
    }

    /**
     * Generate residency warnings for close cases
     */
    public function generateWarnings(array $residencyResults): array
    {
        $warnings = [];

        foreach ($residencyResults as $result) {
            $daysDiff = $result['threshold'] - $result['days_spent'];

            // Warn if within 14 days of threshold
            if ($daysDiff >= -14 && $daysDiff <= 14 && !$result['is_tax_resident']) {
                $warnings[] = [
                    'country' => $result['country_name'],
                    'type' => 'near_threshold',
                    'message' => "You spent {$result['days_spent']} days in {$result['country_name']}, just {$daysDiff} days below the tax residency threshold. Consider this for future planning.",
                ];
            }

            // Warn if barely resident
            if ($result['is_tax_resident'] && $daysDiff < 0 && $daysDiff >= -14) {
                $warnings[] = [
                    'country' => $result['country_name'],
                    'type' => 'barely_resident',
                    'message' => "You became a tax resident of {$result['country_name']} by only " . abs($daysDiff) . " days. Small adjustments to your travel could change this.",
                ];
            }
        }

        return $warnings;
    }
}
