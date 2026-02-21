<?php

namespace App\Services\TaxCalculator;

use App\Models\TaxTreaty;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class TreatyResolutionService
{
    /**
     * Apply treaty rules to prevent double taxation
     */
    public function applyTreaty(int $citizenshipCountryId, array $taxResults, int $taxYear = 2026): array
    {
        $adjustedResults = [];
        $treatiesApplied = [];
        $totalForeignTaxCredit = 0;
        foreach ($taxResults as $result) {
            $residenceCountryId = $result['country_id'];

            // Skip if same country as citizenship, process later for FTC
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
            // Add right after the TaxTreaty query
            Log::info('Treaty lookup', [
                'citizenship_id' => $citizenshipCountryId,
                'residence_id'   => $residenceCountryId,
                'tax_year'       => $taxYear,
                'treaty_found'   => $treaty ? $treaty->treaty_type : 'NONE',
            ]);
            // Skip 'exemption' treaty if user is tax resident (residents usually pay tax)
            if ($treaty && $treaty->treaty_type === 'exemption' && ($result['is_tax_resident'] ?? false)) {
                $adjustedResults[$residenceCountryId] = $result;
                continue;
            }

            if ($treaty) {
                if ($treaty->treaty_type === 'credit' || $treaty->treaty_type === 'totalization') {
                    // Foreign country taxes fully, home country gives credit
                    $totalForeignTaxCredit += $result['tax_due'];
                    $adjustedResults[$residenceCountryId] = $result;

                    $treatiesApplied[] = [
                        'countries' => [
                            Country::find($citizenshipCountryId)->name,
                            Country::find($residenceCountryId)->name,
                        ],
                        'type' => $treaty->treaty_type,
                        'tax_saved' => 0, // Calculated later against home tax
                    ];
                } else {
                    $adjusted = $this->applyTreatyLogic($result, $treaty);
                    $adjustedResults[$residenceCountryId] = $adjusted;

                    $treatiesApplied[] = [
                        'countries' => [
                            Country::find($citizenshipCountryId)->name,
                            Country::find($residenceCountryId)->name,
                        ],
                        'type' => $treaty->treaty_type,
                        'tax_saved' => $result['tax_due'] - $adjusted['tax_due'],
                    ];
                }
            } else {
                $adjustedResults[$residenceCountryId] = $result;
            }
        }

        // Apply Foreign Tax Credit (FTC) to Home Country
        if ($totalForeignTaxCredit > 0 && isset($adjustedResults[$citizenshipCountryId])) {
            $homeTax = $adjustedResults[$citizenshipCountryId]['tax_due'];

            // True Foreign Tax Credit Logic: max(0, home_tax - foreign_tax_paid)
            $newHomeTax = max(0, $homeTax - $totalForeignTaxCredit);
            $taxSaved = $homeTax - $newHomeTax;

            // Find the credit treaties applied to update tax_saved
            foreach ($treatiesApplied as &$ta) {
                if (in_array($ta['type'], ['credit', 'totalization'])) {
                    $ta['tax_saved'] = $taxSaved; // Simplified attribution
                }
            }

            $adjustedResults[$citizenshipCountryId]['tax_due'] = $newHomeTax;
            if (($adjustedResults[$citizenshipCountryId]['allocated_income'] ?? 0) > 0) {
                $adjustedResults[$citizenshipCountryId]['effective_rate'] = round(($newHomeTax / $adjustedResults[$citizenshipCountryId]['allocated_income']) * 100, 2);
            }
            $adjustedResults[$citizenshipCountryId]['treaty_applied'] = 'Foreign Tax Credit Applied';
        } elseif ($totalForeignTaxCredit > 0 && !isset($adjustedResults[$citizenshipCountryId])) {
            // Home country not in breakdown (non-resident by days)
            // FTC means foreign tax paid offsets what home country would charge
            // Update all credit treaties to show the foreign tax paid as the saving
            foreach ($treatiesApplied as &$ta) {
                if (in_array($ta['type'], ['credit', 'totalization'])) {
                    $ta['tax_saved'] = $totalForeignTaxCredit;
                    $ta['note'] = 'Foreign tax paid offsets home country liability';
                }
            }
        }

        return [
            'results' => array_values($adjustedResults),
            'treaties_applied' => $treatiesApplied,
        ];
    }

    /**
     * Apply specific treaty logic for non-credit treaties
     */
    private function applyTreatyLogic(array $taxResult, TaxTreaty $treaty): array
    {
        $adjusted = $taxResult;

        switch ($treaty->treaty_type) {
            case 'exemption':
                // Full exemption
                $adjusted['tax_due'] = 0;
                $adjusted['treaty_applied'] = 'Full Exemption';
                break;

            case 'partial':
                // Partial exemption
                $adjusted['tax_due'] = $taxResult['tax_due'] * 0.5;
                $adjusted['treaty_applied'] = 'Partial Exemption (50%)';
                break;
        }

        // Recalculate effective rate based on new tax_due
        if (isset($adjusted['allocated_income']) && $adjusted['allocated_income'] > 0) {
            $adjusted['effective_rate'] = round(($adjusted['tax_due'] / $adjusted['allocated_income']) * 100, 2);
        } else {
            $adjusted['effective_rate'] = 0;
        }

        return $adjusted;
    }
}
