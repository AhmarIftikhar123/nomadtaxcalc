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
            ['US','GB','credit','US-UK Income Tax Treaty – Foreign tax credit method'],
            ['US','DE','credit','US-Germany Income Tax Treaty – Foreign tax credit method'],
            ['US','FR','credit','US-France Income Tax Treaty – Foreign tax credit method'],
            ['US','CA','credit','US-Canada Income Tax Treaty – Foreign tax credit method'],
            ['US','AU','credit','US-Australia Income Tax Treaty – Foreign tax credit method'],
            ['US','JP','credit','US-Japan Income Tax Treaty – Foreign tax credit method'],
            ['US','KR','credit','US-South Korea Income Tax Treaty – Foreign tax credit method'],
            ['US','ES','credit','US-Spain Income Tax Treaty – Foreign tax credit method'],
            ['US','IT','credit','US-Italy Income Tax Treaty – Foreign tax credit method'],
            ['US','NL','credit','US-Netherlands Income Tax Treaty – Foreign tax credit method'],
            ['US','CH','credit','US-Switzerland Income Tax Treaty – Foreign tax credit method'],
            ['US','IE','credit','US-Ireland Income Tax Treaty – Foreign tax credit method'],
            ['US','SE','credit','US-Sweden Income Tax Treaty – Foreign tax credit method'],
            ['US','AT','credit','US-Austria Income Tax Treaty – Foreign tax credit method'],
            ['US','TH','credit','US-Thailand Income Tax Treaty – Foreign tax credit method'],
            ['US','MX','credit','US-Mexico Income Tax Treaty – Foreign tax credit method'],
            ['US','PT','credit','US-Portugal Income Tax Treaty – Partial credit method'],
            ['US','CZ','credit','US-Czech Republic Income Tax Treaty – Foreign tax credit method'],
            ['US','HU','credit','US-Hungary Income Tax Treaty – Foreign tax credit method'],
            ['US','PL','credit','US-Poland Income Tax Treaty – Foreign tax credit method'],
            ['US','RO','credit','US-Romania Income Tax Treaty – Foreign tax credit method'],
            ['US','BG','credit','US-Bulgaria Income Tax Treaty – Foreign tax credit method'],
            ['US','CY','credit','US-Cyprus Income Tax Treaty – Foreign tax credit method'],
            ['US','EE','credit','US-Estonia Income Tax Treaty – Foreign tax credit method'],
            ['US','PH','credit','US-Philippines Income Tax Treaty – Foreign tax credit method'],
            ['US','ID','credit','US-Indonesia Income Tax Treaty – Foreign tax credit method'],
            ['US','BB','credit','US-Barbados Income Tax Treaty – Foreign tax credit method'],
            // Major UK treaties
            ['GB','DE','credit','UK-Germany Double Taxation Convention – Credit method'],
            ['GB','FR','credit','UK-France Double Taxation Convention – Credit method'],
            ['GB','ES','credit','UK-Spain Double Taxation Convention – Credit method'],
            ['GB','IT','credit','UK-Italy Double Taxation Convention – Credit method'],
            ['GB','PT','credit','UK-Portugal Double Taxation Convention – Credit method'],
            ['GB','NL','credit','UK-Netherlands Double Taxation Convention – Credit method'],
            ['GB','IE','credit','UK-Ireland Double Taxation Convention – Credit method'],
            ['GB','AU','credit','UK-Australia Double Taxation Convention – Credit method'],
            ['GB','CA','credit','UK-Canada Double Taxation Convention – Credit method'],
            ['GB','JP','credit','UK-Japan Double Taxation Convention – Credit method'],
            ['GB','SG','credit','UK-Singapore Double Taxation Convention – Credit method'],
            ['GB','AE','exemption','UK-UAE Double Taxation Convention – Exemption method'],
            ['GB','MT','credit','UK-Malta Double Taxation Convention – Credit method'],
            ['GB','CY','credit','UK-Cyprus Double Taxation Convention – Credit method'],
            // EU cross-treaties (major pairs)
            ['DE','FR','credit','Germany-France Double Tax Convention – Credit method'],
            ['DE','ES','credit','Germany-Spain Double Tax Convention – Credit method'],
            ['DE','IT','credit','Germany-Italy Double Tax Convention – Credit method'],
            ['DE','NL','credit','Germany-Netherlands Double Tax Convention – Credit method'],
            ['DE','AT','credit','Germany-Austria Double Tax Convention – Credit method'],
            ['DE','CH','credit','Germany-Switzerland Double Tax Convention – Credit method'],
            ['DE','PT','credit','Germany-Portugal Double Tax Convention – Credit method'],
            ['DE','SE','credit','Germany-Sweden Double Tax Convention – Credit method'],
            ['FR','ES','credit','France-Spain Double Tax Convention – Credit method'],
            ['FR','IT','credit','France-Italy Double Tax Convention – Credit method'],
            ['FR','PT','credit','France-Portugal Double Tax Convention – Credit method'],
            // Asia-Pacific treaties
            ['SG','AU','exemption','Singapore-Australia DTA – Exemption with progression'],
            ['SG','JP','credit','Singapore-Japan DTA – Foreign tax credit method'],
            ['SG','KR','credit','Singapore-South Korea DTA – Foreign tax credit method'],
            ['SG','TH','credit','Singapore-Thailand DTA – Foreign tax credit method'],
            ['SG','MY','exemption','Singapore-Malaysia DTA – Exemption method'],
            ['SG','ID','credit','Singapore-Indonesia DTA – Foreign tax credit method'],
            ['SG','VN','credit','Singapore-Vietnam DTA – Foreign tax credit method'],
            ['JP','AU','credit','Japan-Australia DTA – Foreign tax credit method'],
            ['JP','KR','credit','Japan-South Korea DTA – Foreign tax credit method'],
            ['JP','TH','credit','Japan-Thailand DTA – Foreign tax credit method'],
            // Americas treaties
            ['CA','AU','credit','Canada-Australia DTA – Foreign tax credit method'],
            ['CA','BR','credit','Canada-Brazil DTA – Foreign tax credit method'],
            ['CA','MX','credit','Canada-Mexico DTA – Foreign tax credit method'],
            ['CA','FR','credit','Canada-France DTA – Foreign tax credit method'],
            ['CA','DE','credit','Canada-Germany DTA – Foreign tax credit method'],
            ['MX','ES','credit','Mexico-Spain DTA – Foreign tax credit method'],
            ['BR','PT','credit','Brazil-Portugal DTA – Foreign tax credit method'],
            // Digital nomad hotspot treaties
            ['PT','BR','credit','Portugal-Brazil DTA – Historical ties, credit method'],
            ['PT','ES','credit','Portugal-Spain DTA – Iberian neighbors, credit method'],
            ['GR','CY','credit','Greece-Cyprus DTA – Credit method'],
            ['HR','DE','credit','Croatia-Germany DTA – Credit method'],
            ['EE','DE','credit','Estonia-Germany DTA – Credit method'],
            ['GE','DE','credit','Georgia-Germany DTA – Credit method'],
            ['MT','IT','credit','Malta-Italy DTA – Credit method'],
            ['PA','MX','credit','Panama-Mexico DTA – Credit method'],
            ['CR','ES','credit','Costa Rica-Spain DTA – Credit method'],
            ['BB','CA','credit','Barbados-Canada DTA – Credit method'],
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
