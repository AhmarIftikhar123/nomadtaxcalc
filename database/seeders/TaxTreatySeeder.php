<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxTreatySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Helper to get country ID by ISO code
        $id = fn(string $iso) => DB::table('countries')->where('iso_code', $iso)->value('id');

        // Treaty pairs: [country_a, country_b, type (credit/exemption), description]
        $treaties = [
            // Major US treaties
            ['US', 'GB', 'credit', 'US-UK Income Tax Treaty – Foreign tax credit method'],
            ['US', 'DE', 'credit', 'US-Germany Income Tax Treaty – Foreign tax credit method'],
            ['US', 'FR', 'credit', 'US-France Income Tax Treaty – Foreign tax credit method'],
            ['US', 'CA', 'credit', 'US-Canada Income Tax Treaty – Foreign tax credit method'],
            ['US', 'AU', 'credit', 'US-Australia Income Tax Treaty – Foreign tax credit method'],
            ['US', 'JP', 'credit', 'US-Japan Income Tax Treaty – Foreign tax credit method'],
            ['US', 'KR', 'credit', 'US-South Korea Income Tax Treaty – Foreign tax credit method'],
            ['US', 'ES', 'credit', 'US-Spain Income Tax Treaty – Foreign tax credit method'],
            ['US', 'IT', 'credit', 'US-Italy Income Tax Treaty – Foreign tax credit method'],
            ['US', 'NL', 'credit', 'US-Netherlands Income Tax Treaty – Foreign tax credit method'],
            ['US', 'CH', 'credit', 'US-Switzerland Income Tax Treaty – Foreign tax credit method'],
            ['US', 'IE', 'credit', 'US-Ireland Income Tax Treaty – Foreign tax credit method'],
            ['US', 'SE', 'credit', 'US-Sweden Income Tax Treaty – Foreign tax credit method'],
            ['US', 'AT', 'credit', 'US-Austria Income Tax Treaty – Foreign tax credit method'],
            ['US', 'TH', 'credit', 'US-Thailand Income Tax Treaty – Foreign tax credit method'],
            ['US', 'MX', 'credit', 'US-Mexico Income Tax Treaty – Foreign tax credit method'],
            ['US', 'PT', 'credit', 'US-Portugal Income Tax Treaty – Partial credit method'],
            ['US', 'CZ', 'credit', 'US-Czech Republic Income Tax Treaty – Foreign tax credit method'],
            ['US', 'HU', 'credit', 'US-Hungary Income Tax Treaty – Foreign tax credit method'],
            ['US', 'PL', 'credit', 'US-Poland Income Tax Treaty – Foreign tax credit method'],
            ['US', 'RO', 'credit', 'US-Romania Income Tax Treaty – Foreign tax credit method'],
            ['US', 'BG', 'credit', 'US-Bulgaria Income Tax Treaty – Foreign tax credit method'],
            ['US', 'CY', 'credit', 'US-Cyprus Income Tax Treaty – Foreign tax credit method'],
            ['US', 'EE', 'credit', 'US-Estonia Income Tax Treaty – Foreign tax credit method'],
            ['US', 'PH', 'credit', 'US-Philippines Income Tax Treaty – Foreign tax credit method'],
            ['US', 'ID', 'credit', 'US-Indonesia Income Tax Treaty – Foreign tax credit method'],
            ['US', 'BB', 'credit', 'US-Barbados Income Tax Treaty – Foreign tax credit method'],
            // Major UK treaties
            ['GB', 'DE', 'credit', 'UK-Germany Double Taxation Convention – Credit method'],
            ['GB', 'FR', 'credit', 'UK-France Double Taxation Convention – Credit method'],
            ['GB', 'ES', 'credit', 'UK-Spain Double Taxation Convention – Credit method'],
            ['GB', 'IT', 'credit', 'UK-Italy Double Taxation Convention – Credit method'],
            ['GB', 'PT', 'credit', 'UK-Portugal Double Taxation Convention – Credit method'],
            ['GB', 'NL', 'credit', 'UK-Netherlands Double Taxation Convention – Credit method'],
            ['GB', 'IE', 'credit', 'UK-Ireland Double Taxation Convention – Credit method'],
            ['GB', 'AU', 'credit', 'UK-Australia Double Taxation Convention – Credit method'],
            ['GB', 'CA', 'credit', 'UK-Canada Double Taxation Convention – Credit method'],
            ['GB', 'JP', 'credit', 'UK-Japan Double Taxation Convention – Credit method'],
            ['GB', 'SG', 'credit', 'UK-Singapore Double Taxation Convention – Credit method'],
            ['GB', 'AE', 'exemption', 'UK-UAE Double Taxation Convention – Exemption method'],
            ['GB', 'MT', 'credit', 'UK-Malta Double Taxation Convention – Credit method'],
            ['GB', 'CY', 'credit', 'UK-Cyprus Double Taxation Convention – Credit method'],
            // EU cross-treaties (major pairs)
            ['DE', 'FR', 'credit', 'Germany-France Double Tax Convention – Credit method'],
            ['DE', 'ES', 'credit', 'Germany-Spain Double Tax Convention – Credit method'],
            ['DE', 'IT', 'credit', 'Germany-Italy Double Tax Convention – Credit method'],
            ['DE', 'NL', 'credit', 'Germany-Netherlands Double Tax Convention – Credit method'],
            ['DE', 'AT', 'credit', 'Germany-Austria Double Tax Convention – Credit method'],
            ['DE', 'CH', 'credit', 'Germany-Switzerland Double Tax Convention – Credit method'],
            ['DE', 'PT', 'credit', 'Germany-Portugal Double Tax Convention – Credit method'],
            ['DE', 'SE', 'credit', 'Germany-Sweden Double Tax Convention – Credit method'],
            ['FR', 'ES', 'credit', 'France-Spain Double Tax Convention – Credit method'],
            ['FR', 'IT', 'credit', 'France-Italy Double Tax Convention – Credit method'],
            ['FR', 'PT', 'credit', 'France-Portugal Double Tax Convention – Credit method'],
            // Asia-Pacific treaties
            ['SG', 'AU', 'exemption', 'Singapore-Australia DTA – Exemption with progression'],
            ['SG', 'JP', 'credit', 'Singapore-Japan DTA – Foreign tax credit method'],
            ['SG', 'KR', 'credit', 'Singapore-South Korea DTA – Foreign tax credit method'],
            ['SG', 'TH', 'credit', 'Singapore-Thailand DTA – Foreign tax credit method'],
            ['SG', 'MY', 'exemption', 'Singapore-Malaysia DTA – Exemption method'],
            ['SG', 'ID', 'credit', 'Singapore-Indonesia DTA – Foreign tax credit method'],
            ['SG', 'VN', 'credit', 'Singapore-Vietnam DTA – Foreign tax credit method'],
            ['JP', 'AU', 'credit', 'Japan-Australia DTA – Foreign tax credit method'],
            ['JP', 'KR', 'credit', 'Japan-South Korea DTA – Foreign tax credit method'],
            ['JP', 'TH', 'credit', 'Japan-Thailand DTA – Foreign tax credit method'],
            // Americas treaties
            ['CA', 'AU', 'credit', 'Canada-Australia DTA – Foreign tax credit method'],
            ['CA', 'BR', 'credit', 'Canada-Brazil DTA – Foreign tax credit method'],
            ['CA', 'MX', 'credit', 'Canada-Mexico DTA – Foreign tax credit method'],
            ['CA', 'FR', 'credit', 'Canada-France DTA – Foreign tax credit method'],
            ['CA', 'DE', 'credit', 'Canada-Germany DTA – Foreign tax credit method'],
            ['MX', 'ES', 'credit', 'Mexico-Spain DTA – Foreign tax credit method'],
            ['BR', 'PT', 'credit', 'Brazil-Portugal DTA – Foreign tax credit method'],
            // Digital nomad hotspot treaties
            ['PT', 'BR', 'credit', 'Portugal-Brazil DTA – Historical ties, credit method'],
            ['PT', 'ES', 'credit', 'Portugal-Spain DTA – Iberian neighbors, credit method'],
            ['GR', 'CY', 'credit', 'Greece-Cyprus DTA – Credit method'],
            ['HR', 'DE', 'credit', 'Croatia-Germany DTA – Credit method'],
            ['EE', 'DE', 'credit', 'Estonia-Germany DTA – Credit method'],
            ['GE', 'DE', 'credit', 'Georgia-Germany DTA – Credit method'],
            ['MT', 'IT', 'credit', 'Malta-Italy DTA – Credit method'],
            ['PA', 'MX', 'credit', 'Panama-Mexico DTA – Credit method'],
            ['CR', 'ES', 'credit', 'Costa Rica-Spain DTA – Credit method'],
            ['BB', 'CA', 'credit', 'Barbados-Canada DTA – Credit method'],
            // India treaties
            ['IN', 'US', 'credit', 'India-US Income Tax Treaty – Foreign tax credit method'],
            ['IN', 'GB', 'credit', 'India-UK Income Tax Treaty – Foreign tax credit method'],
            ['IN', 'SG', 'credit', 'India-Singapore DTA – Foreign tax credit method'],
            ['IN', 'AU', 'credit', 'India-Australia DTA – Foreign tax credit method'],
            ['IN', 'CA', 'credit', 'India-Canada DTA – Foreign tax credit method'],
            ['IN', 'FR', 'credit', 'India-France DTA – Foreign tax credit method'],
            ['IN', 'DE', 'credit', 'India-Germany DTA – Foreign tax credit method'],
            ['IN', 'JP', 'credit', 'India-Japan DTA – Foreign tax credit method'],

            // China treaties
            ['CN', 'US', 'credit', 'China-US Income Tax Treaty – Foreign tax credit method'],
            ['CN', 'GB', 'credit', 'China-UK Income Tax Treaty – Foreign tax credit method'],
            ['CN', 'SG', 'credit', 'China-Singapore DTA – Foreign tax credit method'],
            ['CN', 'AU', 'credit', 'China-Australia DTA – Foreign tax credit method'],
            ['CN', 'CA', 'credit', 'China-Canada DTA – Foreign tax credit method'],
            ['CN', 'FR', 'credit', 'China-France DTA – Foreign tax credit method'],
            ['CN', 'DE', 'credit', 'China-Germany DTA – Foreign tax credit method'],
            ['CN', 'JP', 'credit', 'China-Japan DTA – Foreign tax credit method'],
            ['CN', 'KR', 'credit', 'China-South Korea DTA – Foreign tax credit method'],
            ['CN', 'HK', 'exemption', 'China-Hong Kong DTA – Exemption method'],

            // New Zealand treaties
            ['NZ', 'US', 'credit', 'New Zealand-US Income Tax Treaty – Foreign tax credit method'],
            ['NZ', 'GB', 'credit', 'New Zealand-UK Income Tax Treaty – Foreign tax credit method'],
            ['NZ', 'AU', 'credit', 'New Zealand-Australia DTA – Foreign tax credit method'],
            ['NZ', 'SG', 'credit', 'New Zealand-Singapore DTA – Foreign tax credit method'],
            ['NZ', 'CA', 'credit', 'New Zealand-Canada DTA – Foreign tax credit method'],

            // Hong Kong treaties
            ['HK', 'GB', 'exemption', 'Hong Kong-UK DTA – Exemption method'],
            ['HK', 'SG', 'exemption', 'Hong Kong-Singapore DTA – Exemption method'],
            ['HK', 'AU', 'exemption', 'Hong Kong-Australia DTA – Exemption method'],
            ['HK', 'CA', 'credit', 'Hong Kong-Canada DTA – Foreign tax credit method'],
            ['HK', 'FR', 'credit', 'Hong Kong-France DTA – Foreign tax credit method'],
            ['HK', 'JP', 'credit', 'Hong Kong-Japan DTA – Foreign tax credit method'],

            // Saudi Arabia treaties
            ['SA', 'GB', 'exemption', 'Saudi Arabia-UK DTA – Exemption method'],
            ['SA', 'FR', 'credit', 'Saudi Arabia-France DTA – Foreign tax credit method'],
            ['SA', 'SG', 'exemption', 'Saudi Arabia-Singapore DTA – Exemption method'],
            ['SA', 'IN', 'credit', 'Saudi Arabia-India DTA – Foreign tax credit method'],

            // Turkey treaties
            ['TR', 'US', 'credit', 'Turkey-US Income Tax Treaty – Foreign tax credit method'],
            ['TR', 'GB', 'credit', 'Turkey-UK Income Tax Treaty – Foreign tax credit method'],
            ['TR', 'DE', 'credit', 'Turkey-Germany DTA – Foreign tax credit method'],
            ['TR', 'FR', 'credit', 'Turkey-France DTA – Foreign tax credit method'],
            ['TR', 'NL', 'credit', 'Turkey-Netherlands DTA – Foreign tax credit method'],

            // Norway treaties
            ['NO', 'GB', 'credit', 'Norway-UK DTA – Foreign tax credit method'],
            ['NO', 'US', 'credit', 'Norway-US Income Tax Treaty – Foreign tax credit method'],
            ['NO', 'DE', 'credit', 'Norway-Germany DTA – Foreign tax credit method'],
            ['NO', 'FR', 'credit', 'Norway-France DTA – Foreign tax credit method'],
            ['NO', 'SE', 'credit', 'Norway-Sweden DTA – Foreign tax credit method'],

            // Denmark treaties
            ['DK', 'GB', 'credit', 'Denmark-UK DTA – Foreign tax credit method'],
            ['DK', 'US', 'credit', 'Denmark-US Income Tax Treaty – Foreign tax credit method'],
            ['DK', 'DE', 'credit', 'Denmark-Germany DTA – Foreign tax credit method'],
            ['DK', 'FR', 'credit', 'Denmark-France DTA – Foreign tax credit method'],
            ['DK', 'SE', 'credit', 'Denmark-Sweden DTA – Foreign tax credit method'],

            // Finland treaties
            ['FI', 'GB', 'credit', 'Finland-UK DTA – Foreign tax credit method'],
            ['FI', 'US', 'credit', 'Finland-US Income Tax Treaty – Foreign tax credit method'],
            ['FI', 'DE', 'credit', 'Finland-Germany DTA – Foreign tax credit method'],
            ['FI', 'SE', 'credit', 'Finland-Sweden DTA – Foreign tax credit method'],

            // Belgium treaties
            ['BE', 'US', 'credit', 'Belgium-US Income Tax Treaty – Foreign tax credit method'],
            ['BE', 'GB', 'credit', 'Belgium-UK DTA – Foreign tax credit method'],
            ['BE', 'FR', 'credit', 'Belgium-France DTA – Foreign tax credit method'],
            ['BE', 'DE', 'credit', 'Belgium-Germany DTA – Foreign tax credit method'],
            ['BE', 'NL', 'credit', 'Belgium-Netherlands DTA – Foreign tax credit method'],

            // Israel treaties
            ['IL', 'US', 'credit', 'Israel-US Income Tax Treaty – Foreign tax credit method'],
            ['IL', 'GB', 'credit', 'Israel-UK DTA – Foreign tax credit method'],
            ['IL', 'DE', 'credit', 'Israel-Germany DTA – Foreign tax credit method'],
            ['IL', 'FR', 'credit', 'Israel-France DTA – Foreign tax credit method'],

            // South Africa treaties
            ['ZA', 'GB', 'credit', 'South Africa-UK DTA – Foreign tax credit method'],
            ['ZA', 'US', 'credit', 'South Africa-US Income Tax Treaty – Foreign tax credit method'],
            ['ZA', 'AU', 'credit', 'South Africa-Australia DTA – Foreign tax credit method'],
            ['ZA', 'DE', 'credit', 'South Africa-Germany DTA – Foreign tax credit method'],

            // Chile treaties
            ['CL', 'US', 'credit', 'Chile-US Income Tax Treaty – Foreign tax credit method'],
            ['CL', 'ES', 'credit', 'Chile-Spain DTA – Foreign tax credit method'],
            ['CL', 'BR', 'credit', 'Chile-Brazil DTA – Foreign tax credit method'],
            ['CL', 'MX', 'credit', 'Chile-Mexico DTA – Foreign tax credit method'],

            // Argentina treaties
            ['AR', 'US', 'credit', 'Argentina-US Income Tax Treaty – Foreign tax credit method'],
            ['AR', 'ES', 'credit', 'Argentina-Spain DTA – Foreign tax credit method'],
            ['AR', 'BR', 'credit', 'Argentina-Brazil DTA – Foreign tax credit method'],
            ['AR', 'DE', 'credit', 'Argentina-Germany DTA – Foreign tax credit method'],
        ];

        $records = [];
        foreach ($treaties as [$a, $b, $type, $desc]) {
            $aId = $id($a);
            $bId = $id($b);
            if (!$aId || !$bId) continue;

            $records[] = [
                'country_a_id'        => $aId,
                'country_b_id'        => $bId,
                'treaty_type'         => $type,
                'applicable_tax_year' => 2026,
                'description'         => $desc,
                'is_active'           => true,
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
        }

        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('tax_treaties')->insert($chunk);
        }
    }
}
