<?php

namespace App\Services\TaxCalculator;

use App\Models\TaxTreaty;
use App\Models\Country;

/**
 * Apply tax treaty rules between countries to prevent double taxation.
 *
 * Supports credit, exemption, and partial treaty methods, plus the
 * US-specific Foreign Tax Credit (FTC) mechanism.
 */
class TreatyResolutionService
{
    /**
     * Apply treaty rules to prevent double taxation.
     *
     * Scans for applicable treaties between the citizenship country
     * and each residence country. Applies the appropriate relief method
     * and handles US FTC as a special case.
     *
     * @param  int    $citizenshipCountryId  User's citizenship country ID.
     * @param  array  $taxResults            Per-country tax results.
     * @param  int    $taxYear               Tax year for treaty lookup.
     * @return array{adjustedResults: array, treatiesApplied: array}
     */
    public function applyTreaty(int $citizenshipCountryId, array $taxResults, int $taxYear = 2026): array
    {
        $adjustedResults       = [];
        $treatiesApplied       = [];
        $totalForeignTaxCredit = 0;

        foreach ($taxResults as $result) {
            $residenceCountryId = $result['country_id'];

            // Same country as citizenship — process later for FTC
            if ($residenceCountryId === $citizenshipCountryId) {
                $adjustedResults[$residenceCountryId] = $result;
                continue;
            }

            // Look for treaty
            $treaty = TaxTreaty::active()
                ->between($citizenshipCountryId, $residenceCountryId)
                ->orderByRaw(
                    'CASE WHEN applicable_tax_year = ? THEN 0 ELSE 1 END',
                    [$taxYear]
                )
                ->orderByDesc('applicable_tax_year')
                ->first();

            // Skip 'exemption' treaty if user is tax resident (residents usually pay tax)
            if ($treaty && $treaty->treaty_type === 'exemption' && ($result['is_tax_resident'] ?? false)) {
                $adjustedResults[$residenceCountryId] = $result;
                continue;
            }

            if ($treaty) {
                if ($treaty->treaty_type === 'credit' || $treaty->treaty_type === 'totalization') {
                    // Foreign country taxes fully; home country gives credit later
                    $totalForeignTaxCredit += $result['tax_due'];
                    $adjustedResults[$residenceCountryId] = $result;

                    $treatiesApplied[] = [
                        'countries'          => [
                            Country::find($citizenshipCountryId)->name,
                            Country::find($residenceCountryId)->name,
                        ],
                        'type'               => $treaty->treaty_type,
                        'tax_saved'          => 0,            // filled in below
                        'foreign_tax_paid'   => $result['tax_due'], // ← NEW: always store this
                    ];
                } else {
                    $adjusted = $this->applyTreatyLogic($result, $treaty);
                    $adjustedResults[$residenceCountryId] = $adjusted;

                    $treatiesApplied[] = [
                        'countries'        => [
                            Country::find($citizenshipCountryId)->name,
                            Country::find($residenceCountryId)->name,
                        ],
                        'type'             => $treaty->treaty_type,
                        'tax_saved'        => $result['tax_due'] - $adjusted['tax_due'],
                        'foreign_tax_paid' => $result['tax_due'],
                    ];
                }
            } else {
                $adjustedResults[$residenceCountryId] = $result;
            }
        }

        // ── Apply Foreign Tax Credit (FTC) to Home Country ──────────────────

        if ($totalForeignTaxCredit > 0 && isset($adjustedResults[$citizenshipCountryId])) {

            // HOME COUNTRY IS IN RESULTS (e.g. user spent some days in the US)
            // True FTC: home tax reduced by foreign tax already paid
            $homeTax    = $adjustedResults[$citizenshipCountryId]['tax_due'];
            $newHomeTax = max(0, $homeTax - $totalForeignTaxCredit);
            $taxSaved   = $homeTax - $newHomeTax; // the real saving

            foreach ($treatiesApplied as &$ta) {
                if (in_array($ta['type'], ['credit', 'totalization'])) {
                    $ta['tax_saved'] = round($taxSaved, 2);
                    $ta['note']      = 'Foreign Tax Credit applied — US tax reduced by foreign taxes paid';
                }
            }
            unset($ta);

            $adjustedResults[$citizenshipCountryId]['tax_due']        = $newHomeTax;
            $adjustedResults[$citizenshipCountryId]['treaty_applied'] = 'Foreign Tax Credit Applied';
           
            $adjustedResults[$citizenshipCountryId]['ftc_applied']  = round($taxSaved, 2);
            $adjustedResults[$citizenshipCountryId]['ftc_note']     =
                'US tax reduced by $' . number_format($taxSaved, 2) .
                ' via Foreign Tax Credit (Spain taxes paid)';
            if (($adjustedResults[$citizenshipCountryId]['allocated_income'] ?? 0) > 0) {
                $adjustedResults[$citizenshipCountryId]['effective_rate'] = round(
                    ($newHomeTax / $adjustedResults[$citizenshipCountryId]['allocated_income']) * 100,
                    2
                );
            }
        } elseif ($totalForeignTaxCredit > 0 && ! isset($adjustedResults[$citizenshipCountryId])) {

            // HOME COUNTRY NOT IN RESULTS (e.g. US citizen spent 0 days in the US)
            // We have no calculated home-country tax to offset against,
            // so tax_saved = 0 is the honest value.
            // We store foreign_tax_paid so the frontend can display
            // "Double Taxation Protected: $60,158" without calling it a "saving".
            foreach ($treatiesApplied as &$ta) {
                if (in_array($ta['type'], ['credit', 'totalization'])) {
                    $ta['tax_saved']        = 0; // ← FIX: was $totalForeignTaxCredit (wrong)
                    $ta['foreign_tax_paid'] = round($totalForeignTaxCredit, 2);
                    $ta['note']             = 'Foreign taxes paid qualify as credit against any future US liability';
                }
            }
            unset($ta);
        }

        return [
            'results'          => array_values($adjustedResults),
            'treaties_applied' => $treatiesApplied,
        ];
    }

    /**
     * Apply specific treaty logic for non-credit treaties.
     */
    private function applyTreatyLogic(array $taxResult, TaxTreaty $treaty): array
    {
        $adjusted = $taxResult;

        switch ($treaty->treaty_type) {
            case 'exemption':
                $adjusted['tax_due']        = 0;
                $adjusted['treaty_applied'] = 'Full Exemption';
                break;

            case 'partial':
                $adjusted['tax_due']        = $taxResult['tax_due'] * 0.5;
                $adjusted['treaty_applied'] = 'Partial Exemption (50%)';
                break;
        }

        if (isset($adjusted['allocated_income']) && $adjusted['allocated_income'] > 0) {
            $adjusted['effective_rate'] = round(
                ($adjusted['tax_due'] / $adjusted['allocated_income']) * 100,
                2
            );
        } else {
            $adjusted['effective_rate'] = 0;
        }

        return $adjusted;
    }
}
