<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxTreatySeeder extends Seeder
{
    public function run(): void
    {
        $treaties = [
            [
                'country_id_1' => 1,
                'country_id_2' => 4,
                'treaty_name' => 'Portugal-UK Income Tax Treaty',
                'treaty_type' => 'income_tax',
                'effective_year' => 2026,
                'key_benefits' => 'Elimination of double taxation. Dividend withholding reduced to 15%. Interest and royalties at reduced rates.',
                'treaty_document_url' => 'https://www.portaldasfinancas.gov.pt',
                'is_active' => true,
            ],
            [
                'country_id_1' => 2,
                'country_id_2' => 6,
                'treaty_name' => 'Spain-France Income Tax Treaty',
                'treaty_type' => 'income_tax',
                'effective_year' => 2026,
                'key_benefits' => 'EU member states coordination. Double taxation relief. Mutual assistance in tax matters.',
                'treaty_document_url' => 'https://www.agenciatributaria.es',
                'is_active' => true,
            ],
            [
                'country_id_1' => 3,
                'country_id_2' => 4,
                'treaty_name' => 'US-UK Income Tax Treaty',
                'treaty_type' => 'income_tax',
                'effective_year' => 2026,
                'key_benefits' => 'Foreign Tax Credit eligibility. Reduced withholding on dividends (15%), interest (0%), royalties (0%).',
                'treaty_document_url' => 'https://www.irs.gov',
                'is_active' => true,
            ],
            [
                'country_id_1' => 5,
                'country_id_2' => 6,
                'treaty_name' => 'Germany-France Income Tax Treaty',
                'treaty_type' => 'income_tax',
                'effective_year' => 2026,
                'key_benefits' => 'Double tax elimination. Employment income taxation rights. Pension coordination.',
                'treaty_document_url' => 'https://www.bzst.bund.de',
                'is_active' => true,
            ],
            [
                'country_id_1' => 7,
                'country_id_2' => 10,
                'treaty_name' => 'Thailand-Singapore Income Tax Treaty',
                'treaty_type' => 'income_tax',
                'effective_year' => 2026,
                'key_benefits' => 'Business profit taxation coordination. Reduced withholding on dividends (15%), interest (10%).',
                'treaty_document_url' => 'https://www.rd.go.th',
                'is_active' => true,
            ],
        ];

        DB::table('tax_treaties')->insert($treaties);
    }
}
