<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Patches countries table with worldwide_income_threshold values.
 * Runs AFTER CountrySeeder to update the new columns worldwide_income_threshold added in Phase 1A.
 *
 * worldwide_income_threshold: Days threshold at which full worldwide income is taxed.
 * - null        → use tax_residency_days as fallback (most countries)
 * - 0           → always tax full worldwide income regardless of days (US citizens)
 * - custom int  → specific threshold different from residency threshold
 */
class CountryDataPatchSeeder extends Seeder
{
    public function run(): void
    {
        // Countries that ALWAYS tax worldwide income regardless of days
        // (citizenship-based taxation)
        $alwaysWorldwide = [
            'US' => 0,   // US citizens taxed worldwide regardless of residency
            'ER' => 0,   // Eritrea also uses citizenship-based taxation
        ];

        // Countries with worldwide_income_threshold different from tax_residency_days
        $customThresholds = [
            // UK: Statutory residence test has complex rules, effectively ~183 days
            // but with additional presence tests (16-day and 46-day rules)
            'GB' => 183,
            // France: 183 days OR center of economic interests
            'FR' => 183,
            // Germany: 183 days or habitual abode (6+ months)
            'DE' => 183,
            // Spain: 183 days in calendar year
            'ES' => 183,
            // Italy: 183 days (changed to 183 from 184 in 2024)
            'IT' => 183,
            // Australia: Complex test, 183-day rule is primary
            'AU' => 183,
            // Japan: Permanent residents taxed on all worldwide income
            // Non-permanent (~5 yr) taxed on Japan-source + remitted foreign
            'JP' => 183,
            // Canada: 183 days in year = deemed resident
            'CA' => 183,
            // South Korea: 183 days
            'KR' => 183,
            // India: 182 days (not 183!)
            'IN' => 182,
            // China: 183 days in calendar year
            'CN' => 183,
            // Brazil: 183 days in 12-month period
            'BR' => 183,
            // Netherlands: domestic ties + 183 days
            'NL' => 183,
            // Sweden: 183 days in 6-month period OR habitual abode
            'SE' => 183,
            // Norway: 183 days in 12-month period
            'NO' => 183,
            // Denmark: 183 days in consecutive period or 6 months
            'DK' => 183,
            // Finland: 183 days or permanent home
            'FI' => 183,
            // Belgium: 183 days or registered domicile
            'BE' => 183,
            // Austria: 183 days or habitual abode (6+ months)
            'AT' => 183,
            // Switzerland: 90 days for gainful employment, 183 days otherwise
            'CH' => 90,
            // Ireland: 183 days in one year or 280 days over 2 years
            'IE' => 183,
            // Portugal: 183 days or habitual residence
            'PT' => 183,
            // Greece: 183 days
            'GR' => 183,
            // Mexico: 183 days
            'MX' => 183,
            // Turkey: 183 days in calendar year
            'TR' => 183,
            // Poland: 183 days
            'PL' => 183,
            // Czech Republic: 183 days
            'CZ' => 183,
            // Hungary: 183 days or permanent home
            'HU' => 183,
            // Romania: 183 days in 12-month period
            'RO' => 183,
            // Bulgaria: 183 days
            'BG' => 183,
            // Croatia: 183 days
            'HR' => 183,
            // Israel: 183 days in year OR 30 days + 425 days over 3 years
            'IL' => 183,
            // South Africa: 91 days current year + 91 days each of prior 5 years + 915 total
            'ZA' => 91,
            // Pakistan: 182 days in tax year
            'PK' => 182,
        ];

        // Apply always-worldwide thresholds
        foreach ($alwaysWorldwide as $iso => $threshold) {
            DB::table('countries')
                ->where('iso_code', $iso)
                ->update(['worldwide_income_threshold' => $threshold]);
        }

        // Apply custom thresholds
        foreach ($customThresholds as $iso => $threshold) {
            DB::table('countries')
                ->where('iso_code', $iso)
                ->update(['worldwide_income_threshold' => $threshold]);
        }
    }
}
