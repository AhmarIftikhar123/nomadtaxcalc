<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 2026 US State Income Tax Brackets (all 41 income-tax states).
 * CA and NY are already in TaxBracketSeeder — this adds the remaining 39.
 * Sources: state revenue department websites, Tax Foundation, CCH/Wolters Kluwer.
 * Single/individual filer rates used as defaults.
 */
class UsStateBracketSeeder extends Seeder
{
    public function run(): void
    {
        $taxTypeId = DB::table('tax_types')->where('key', 'income_tax')->value('id');
        $usCountryId = DB::table('countries')->where('iso_code', 'US')->value('id');
        if (!$usCountryId || !$taxTypeId) return;

        $now = now();
        $records = [];

        // State brackets: [min, max, rate] — Single filer, 2026 projected
        // Flat-tax states have a single bracket [0, null, rate]
        $stateBrackets = [
            // Alabama — Progressive 2%-5%
            'AL' => [
                [0, 500, 2.00],
                [500, 3000, 4.00],
                [3000, null, 5.00],
            ],
            // Arizona — Flat 2.5% (since 2023)
            'AZ' => [
                [0, null, 2.50],
            ],
            // Arkansas — Progressive 0%-3.9% (2026)
            'AR' => [
                [0, 5100, 0.00],
                [5100, 10300, 2.00],
                [10300, 15500, 3.00],
                [15500, 24700, 3.40],
                [24700, null, 3.90],
            ],
            // Colorado — Flat 4.25% (2026 projected, was 4.4% in 2024)
            'CO' => [
                [0, null, 4.25],
            ],
            // Connecticut — Progressive 3%-6.99%
            'CT' => [
                [0, 10000, 3.00],
                [10000, 50000, 5.00],
                [50000, 100000, 5.50],
                [100000, 200000, 6.00],
                [200000, 250000, 6.50],
                [250000, 500000, 6.90],
                [500000, null, 6.99],
            ],
            // Delaware — Progressive 0%-6.60%
            'DE' => [
                [0, 2000, 0.00],
                [2000, 5000, 2.20],
                [5000, 10000, 3.90],
                [10000, 20000, 4.80],
                [20000, 25000, 5.20],
                [25000, 60000, 5.55],
                [60000, null, 6.60],
            ],
            // Georgia — Flat 5.39% (2026 projected, was 5.49% in 2025)
            'GA' => [
                [0, null, 5.39],
            ],
            // Hawaii — Progressive 1.40%-11%
            'HI' => [
                [0, 2400, 1.40],
                [2400, 4800, 3.20],
                [4800, 9600, 5.50],
                [9600, 14400, 6.40],
                [14400, 19200, 6.80],
                [19200, 24000, 7.20],
                [24000, 36000, 7.60],
                [36000, 48000, 7.90],
                [48000, 150000, 8.25],
                [150000, 175000, 9.00],
                [175000, 200000, 10.00],
                [200000, null, 11.00],
            ],
            // Idaho — Flat 5.695% (2026 projected)
            'ID' => [
                [0, null, 5.695],
            ],
            // Illinois — Flat 4.95%
            'IL' => [
                [0, null, 4.95],
            ],
            // Indiana — Flat 3.05% (2026 projected, decreasing schedule)
            'IN' => [
                [0, null, 3.05],
            ],
            // Iowa — Flat 3.80% (2026, was 3.90% in 2025)
            'IA' => [
                [0, null, 3.80],
            ],
            // Kansas — Progressive 3.10%-5.70%
            'KS' => [
                [0, 15000, 3.10],
                [15000, 30000, 5.25],
                [30000, null, 5.70],
            ],
            // Kentucky — Flat 4.00% (since 2024)
            'KY' => [
                [0, null, 4.00],
            ],
            // Louisiana — Progressive 1.85%-4.25% (2026)
            'LA' => [
                [0, 12500, 1.85],
                [12500, 50000, 3.50],
                [50000, null, 4.25],
            ],
            // Maine — Progressive 5.80%-7.15%
            'ME' => [
                [0, 26050, 5.80],
                [26050, 61600, 6.75],
                [61600, null, 7.15],
            ],
            // Maryland — Progressive 2%-5.75%
            'MD' => [
                [0, 1000, 2.00],
                [1000, 2000, 3.00],
                [2000, 3000, 4.00],
                [3000, 100000, 4.75],
                [100000, 125000, 5.00],
                [125000, 150000, 5.25],
                [150000, 250000, 5.50],
                [250000, null, 5.75],
            ],
            // Massachusetts — Flat 5.00%
            'MA' => [
                [0, null, 5.00],
            ],
            // Michigan — Flat 4.05% (2026 projected)
            'MI' => [
                [0, null, 4.05],
            ],
            // Minnesota — Progressive 5.35%-9.85%
            'MN' => [
                [0, 31690, 5.35],
                [31690, 104090, 6.80],
                [104090, 183340, 7.85],
                [183340, null, 9.85],
            ],
            // Mississippi — Flat 4.40% (2026 projected, was 4.60% in 2025)
            'MS' => [
                [0, 10000, 0.00],
                [10000, null, 4.40],
            ],
            // Missouri — Progressive 2%-4.80% (2026)
            'MO' => [
                [0, 1207, 2.00],
                [1207, 2414, 2.50],
                [2414, 3621, 3.00],
                [3621, 4828, 3.50],
                [4828, 6035, 4.00],
                [6035, 7242, 4.50],
                [7242, 8449, 4.80],
                [8449, null, 4.80],
            ],
            // Montana — Progressive 4.70%-5.90% (2026)
            'MT' => [
                [0, 20500, 4.70],
                [20500, null, 5.90],
            ],
            // Nebraska — Progressive 2.46%-5.84% (2026)
            'NE' => [
                [0, 3700, 2.46],
                [3700, 22170, 3.51],
                [22170, 35730, 5.01],
                [35730, null, 5.84],
            ],
            // New Jersey — Progressive 1.40%-10.75%
            'NJ' => [
                [0, 20000, 1.40],
                [20000, 35000, 1.75],
                [35000, 40000, 3.50],
                [40000, 75000, 5.53],
                [75000, 500000, 6.37],
                [500000, 1000000, 8.97],
                [1000000, null, 10.75],
            ],
            // New Mexico — Progressive 1.70%-5.90%
            'NM' => [
                [0, 5500, 1.70],
                [5500, 11000, 3.20],
                [11000, 16000, 4.70],
                [16000, 210000, 4.90],
                [210000, null, 5.90],
            ],
            // North Carolina — Flat 4.25% (2026, decreasing schedule)
            'NC' => [
                [0, null, 4.25],
            ],
            // North Dakota — Flat 1.95% (2026, simplified from progressive)
            'ND' => [
                [0, null, 1.95],
            ],
            // Ohio — Progressive 0%-3.50% (2026)
            'OH' => [
                [0, 26050, 0.00],
                [26050, 100000, 2.75],
                [100000, null, 3.50],
            ],
            // Oklahoma — Progressive 0.25%-4.75%
            'OK' => [
                [0, 1000, 0.25],
                [1000, 2500, 0.75],
                [2500, 3750, 1.75],
                [3750, 4900, 2.75],
                [4900, 7200, 3.75],
                [7200, null, 4.75],
            ],
            // Oregon — Progressive 4.75%-9.90%
            'OR' => [
                [0, 4050, 4.75],
                [4050, 10200, 6.75],
                [10200, 125000, 8.75],
                [125000, null, 9.90],
            ],
            // Pennsylvania — Flat 3.07%
            'PA' => [
                [0, null, 3.07],
            ],
            // Rhode Island — Progressive 3.75%-5.99%
            'RI' => [
                [0, 77450, 3.75],
                [77450, 176050, 4.75],
                [176050, null, 5.99],
            ],
            // South Carolina — Progressive 0%-6.20% (2026)
            'SC' => [
                [0, 3460, 0.00],
                [3460, 17330, 3.00],
                [17330, null, 6.20],
            ],
            // Utah — Flat 4.55% (2026)
            'UT' => [
                [0, null, 4.55],
            ],
            // Vermont — Progressive 3.35%-8.75%
            'VT' => [
                [0, 45400, 3.35],
                [45400, 110050, 6.60],
                [110050, 229950, 7.60],
                [229950, null, 8.75],
            ],
            // Virginia — Progressive 2%-5.75%
            'VA' => [
                [0, 3000, 2.00],
                [3000, 5000, 3.00],
                [5000, 17000, 5.00],
                [17000, null, 5.75],
            ],
            // West Virginia — Progressive 2.36%-5.12% (2026)
            'WV' => [
                [0, 10000, 2.36],
                [10000, 25000, 3.15],
                [25000, 40000, 3.54],
                [40000, 60000, 4.72],
                [60000, null, 5.12],
            ],
            // Wisconsin — Progressive 3.50%-7.65%
            'WI' => [
                [0, 14320, 3.50],
                [14320, 28640, 4.40],
                [28640, 315310, 5.30],
                [315310, null, 7.65],
            ],
        ];

        foreach ($stateBrackets as $stateCode => $brackets) {
            $stateId = DB::table('states')
                ->where('country_id', $usCountryId)
                ->where('code', $stateCode)
                ->value('id');
            if (!$stateId) continue;

            foreach ($brackets as $bracket) {
                $records[] = [
                    'country_id'    => $usCountryId,
                    'state_id'      => $stateId,
                    'tax_type_id'   => $taxTypeId,
                    'tax_year'      => 2026,
                    'min_income'    => $bracket[0],
                    'max_income'    => $bracket[1],
                    'rate'          => $bracket[2],
                    'has_cap'       => false,
                    'annual_cap'    => null,
                    'currency_code' => 'USD',
                    'is_active'     => true,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
        }

        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('tax_brackets')->insert($chunk);
        }
    }
}
