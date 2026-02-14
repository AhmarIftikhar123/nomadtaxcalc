<?php

namespace App\Services\TaxCalculator;

use App\Models\TaxTreaty;
use App\Models\Country;

class TreatyResolutionService
{
    /**
     * Apply treaty rules to prevent double taxation
     */
    public function applyTreaty(int $citizenshipCountryId, array $taxResults): array
    {
        $adjustedResults = [];
        $treatiesApplied = [];

        foreach ($taxResults as $result) {
            $residenceCountryId = $result['country_id'];

            // Skip if same country as citizenship
            if ($residenceCountryId === $citizenshipCountryId) {
                $adjustedResults[] = $result;
                continue;
            }

            // Look for treaty
            $treaty = TaxTreaty::active()
                ->between($citizenshipCountryId, $residenceCountryId)
                ->where('applicable_tax_year', 2026)
                ->first();

            if ($treaty) {
                $adjusted = $this->applyTreatyLogic($result, $treaty);
                $adjustedResults[] = $adjusted;

                $treatiesApplied[] = [
                    'countries' => [
                        Country::find($citizenshipCountryId)->name,
                        Country::find($residenceCountryId)->name,
                    ],
                    'type' => $treaty->treaty_type,
                    'tax_saved' => $result['tax_due'] - $adjusted['tax_due'],
                ];
            } else {
                $adjustedResults[] = $result;
            }
        }

        return [
            'results' => $adjustedResults,
            'treaties_applied' => $treatiesApplied,
        ];
    }

    /**
     * Apply specific treaty logic
     */
    private function applyTreatyLogic(array $taxResult, TaxTreaty $treaty): array
    {
        $adjusted = $taxResult;

        switch ($treaty->treaty_type) {
            case 'credit':
                // Foreign tax credit - reduce by credit amount
                // Simplified: assume full credit
                $adjusted['tax_due'] = max(0, $taxResult['tax_due'] * 0.85); // 15% credit
                $adjusted['treaty_applied'] = 'Foreign Tax Credit';
                break;

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

        return $adjusted;
    }
}
