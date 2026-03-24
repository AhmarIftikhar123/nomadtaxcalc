<?php

namespace App\Services\TaxCalculator;

use App\Models\Country;
use Illuminate\Support\Facades\Log;

/**
 * Determines tax residency status for each country a user visited.
 *
 * Handles day-threshold residency (most countries) and citizenship-based
 * residency (e.g. United States). Generates warnings for near-threshold cases.
 */
class ResidencyDeterminationService
{
    /**
     * Cached US country model — loaded once per request.
     */
    private ?Country $usCountry = null;

    /**
     * Determine tax residency status for each visited country.
     *
     * Citizenship-based countries (e.g. US with `taxes_worldwide_income = true`)
     * always mark the citizen as tax-resident regardless of days spent.
     * Other countries use the `tax_residency_days` threshold with arrival/departure adjustments.
     *
     * @param  array  $visitedCountries      Raw period arrays from the frontend.
     * @param  int    $citizenshipCountryId   The user's citizenship country ID (0 if unknown).
     * @return array                          Per-country residency determination results.
     */
    public function determine(array $visitedCountries, int $citizenshipCountryId = 0): array
    {
        $results = [];

        // Pre-load all country IDs in one query to avoid N+1
        $countryIds = array_unique(array_column($visitedCountries, 'country_id'));
        $countriesMap = Country::whereIn('id', $countryIds)->get()->keyBy('id');

        foreach ($visitedCountries as $visit) {
            $country = $countriesMap[$visit['country_id']] ?? null;
            if (! $country) {
                Log::warning('ResidencyDeterminationService: country not found', [
                    'country_id' => $visit['country_id'],
                ]);
                continue;
            }

            // Raw days from either key name (frontend sends 'days', DB sends 'days_spent')
            $rawDays  = isset($visit['days']) ? (int) $visit['days'] : (int) $visit['days_spent'];
            $threshold = (int) $country->tax_residency_days;

            // ── Adjusted days for arrival/departure counting rules ────────
            $adjustedDays = $rawDays;
            if (! $country->counts_arrival_day)   $adjustedDays -= 1;
            if (! $country->counts_departure_day) $adjustedDays -= 1;
            $adjustedDays = max(0, $adjustedDays); // never go negative

            // ── Citizenship-based residency (e.g. United States) ─────────
            // If the country taxes worldwide income based on citizenship
            // (taxes_worldwide_income = true), and the user IS a citizen
            // of this country, they are ALWAYS a tax resident — days don't matter.
            $isCitizenshipBased = $country->taxes_worldwide_income === true
                && $country->tax_basis === 'worldwide';

            $isCitizen = $citizenshipCountryId > 0
                && $country->id === $citizenshipCountryId;

            if ($isCitizenshipBased && $isCitizen) {
                // Always tax resident — citizenship overrides day count
                $isResident = true;
                $reason     = $this->getCitizenshipResidencyReason($rawDays, $threshold, $country->name);
            } else {
                // Standard day-threshold residency
                $isResident = $adjustedDays >= $threshold;
                $reason     = $this->getResidencyReason($adjustedDays, $threshold, $isResident);
            }

            $results[] = [
                'country_id'             => $country->id,
                'country_name'           => $country->name,
                'country_code'           => $country->iso_code,
                'days_spent'             => $rawDays,
                'adjusted_days'          => $adjustedDays,
                'threshold'              => $threshold,
                'is_tax_resident'        => $isResident,
                'is_citizenship_based'   => $isCitizenshipBased && $isCitizen,
                'taxes_worldwide_income' => (bool) $country->taxes_worldwide_income,
                'tax_basis'              => $country->tax_basis,
                'local_income_currency'  => $visit['local_income_currency'] ?? null,
                'reason'                 => $reason,
            ];
        }

        return $results;
    }

    /**
     * Generate residency warnings for borderline cases.
     *
     * Warnings are produced when a user is within 14 days of the threshold:
     *  - `near_threshold`: Non-resident but close to becoming one.
     *  - `barely_resident`: Resident by a slim margin.
     *
     * Citizenship-based countries are excluded (days are irrelevant).
     * Uses `adjusted_days` (accounting for arrival/departure rules) for
     * accurate comparison against the threshold.
     *
     * @param  array  $residencyResults  Results from determine().
     * @return array                      Warning entries for the frontend.
     */
    public function generateWarnings(array $residencyResults): array
    {
        $warnings = [];

        foreach ($residencyResults as $result) {
            if ($result['is_citizenship_based'] ?? false) {
                continue;
            }

            // BUG-6 FIX: Use adjusted_days (accounts for arrival/departure
            // counting rules) instead of raw days_spent for threshold comparison.
            $days     = $result['adjusted_days'] ?? $result['days_spent'];
            $daysDiff = $result['threshold'] - $days;

            if (! $result['is_tax_resident'] && $daysDiff >= 0 && $daysDiff <= 14) {
                $warnings[] = [
                    'country' => $result['country_name'],
                    'type'    => 'near_threshold',
                    'message' => "You spent {$days} days in {$result['country_name']}, "
                        . "only {$daysDiff} days below the tax residency threshold. "
                        . "Consider this for future travel planning.",
                ];
            }

            if ($result['is_tax_resident'] && $daysDiff < 0 && $daysDiff >= -14) {
                $warnings[] = [
                    'country' => $result['country_name'],
                    'type'    => 'barely_resident',
                    'message' => "You became a tax resident of {$result['country_name']} "
                        . "by only " . abs($daysDiff) . " days. "
                        . "Small adjustments to your travel could avoid tax residency next year.",
                ];
            }
        }

        return $warnings;
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Human-readable reason for standard day-threshold residency.
     *
     * @param  int   $adjustedDays  Days after arrival/departure adjustments.
     * @param  int   $threshold     Country's residency day threshold.
     * @param  bool  $isResident    Whether the user is considered resident.
     * @return string
     */
    private function getResidencyReason(int $adjustedDays, int $threshold, bool $isResident): string
    {
        if ($isResident) {
            $over = $adjustedDays - $threshold;
            return "Spent {$adjustedDays} days, exceeding the {$threshold}-day threshold by {$over} days.";
        }

        $remaining = $threshold - $adjustedDays;
        return "Spent {$adjustedDays} days, {$remaining} days below the {$threshold}-day threshold.";
    }

    /**
     * Human-readable reason for citizenship-based residency (e.g. US).
     *
     * @param  int     $days         Raw days spent in the country.
     * @param  int     $threshold    Country's residency day threshold.
     * @param  string  $countryName  Name of the country.
     * @return string
     */
    private function getCitizenshipResidencyReason(int $days, int $threshold, string $countryName): string
    {
        return "{$countryName} taxes citizens on worldwide income regardless of physical presence. "
            . "Days spent ({$days}) do not affect tax residency status.";
    }
}