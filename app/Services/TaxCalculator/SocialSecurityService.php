<?php

namespace App\Services\TaxCalculator;

use App\Models\Country;
use App\Models\TaxTreaty;

class SocialSecurityService
{
    /**
     * Determine if social security applies based on totalization agreements
     */
    public function checkTotalization(int $citizenshipCountryId, int $residenceCountryId, int $taxYear = 2026): bool
    {
        $treaty = TaxTreaty::active()
            ->between($citizenshipCountryId, $residenceCountryId)
            ->where('applicable_tax_year', $taxYear)
            ->where('treaty_type', 'totalization')
            ->first();

        return $treaty !== null;
    }

    /**
     * Get social security tax rate logic if necessary (simplified stub)
     */
    public function calculateSocialSecurity(Country $country, float $income): float
    {
        // For demonstration, you could have country-specific social security rates here
        // If a totalization agreement exists, we bypass this in the TaxCalculation logic
        return 0; // Handled primarily by tax brackets currently in DB, this is for dedicated logic if needed
    }
}
