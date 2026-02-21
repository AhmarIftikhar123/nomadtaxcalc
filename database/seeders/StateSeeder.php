<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $usCountryId = DB::table('countries')->where('iso_code', 'US')->value('id');

        if (!$usCountryId) {
            return;
        }

        $states = [
            ['name' => 'Alabama', 'code' => 'AL', 'has_income_tax' => true],
            ['name' => 'Alaska', 'code' => 'AK', 'has_income_tax' => false],
            ['name' => 'Arizona', 'code' => 'AZ', 'has_income_tax' => true],
            ['name' => 'Arkansas', 'code' => 'AR', 'has_income_tax' => true],
            ['name' => 'California', 'code' => 'CA', 'has_income_tax' => true],
            ['name' => 'Colorado', 'code' => 'CO', 'has_income_tax' => true],
            ['name' => 'Connecticut', 'code' => 'CT', 'has_income_tax' => true],
            ['name' => 'Delaware', 'code' => 'DE', 'has_income_tax' => true],
            ['name' => 'Florida', 'code' => 'FL', 'has_income_tax' => false],
            ['name' => 'Georgia', 'code' => 'GA', 'has_income_tax' => true],
            ['name' => 'Hawaii', 'code' => 'HI', 'has_income_tax' => true],
            ['name' => 'Idaho', 'code' => 'ID', 'has_income_tax' => true],
            ['name' => 'Illinois', 'code' => 'IL', 'has_income_tax' => true],
            ['name' => 'Indiana', 'code' => 'IN', 'has_income_tax' => true],
            ['name' => 'Iowa', 'code' => 'IA', 'has_income_tax' => true],
            ['name' => 'Kansas', 'code' => 'KS', 'has_income_tax' => true],
            ['name' => 'Kentucky', 'code' => 'KY', 'has_income_tax' => true],
            ['name' => 'Louisiana', 'code' => 'LA', 'has_income_tax' => true],
            ['name' => 'Maine', 'code' => 'ME', 'has_income_tax' => true],
            ['name' => 'Maryland', 'code' => 'MD', 'has_income_tax' => true],
            ['name' => 'Massachusetts', 'code' => 'MA', 'has_income_tax' => true],
            ['name' => 'Michigan', 'code' => 'MI', 'has_income_tax' => true],
            ['name' => 'Minnesota', 'code' => 'MN', 'has_income_tax' => true],
            ['name' => 'Mississippi', 'code' => 'MS', 'has_income_tax' => true],
            ['name' => 'Missouri', 'code' => 'MO', 'has_income_tax' => true],
            ['name' => 'Montana', 'code' => 'MT', 'has_income_tax' => true],
            ['name' => 'Nebraska', 'code' => 'NE', 'has_income_tax' => true],
            ['name' => 'Nevada', 'code' => 'NV', 'has_income_tax' => false],
            ['name' => 'New Hampshire', 'code' => 'NH', 'has_income_tax' => false], // Tax on interest/dividends only
            ['name' => 'New Jersey', 'code' => 'NJ', 'has_income_tax' => true],
            ['name' => 'New Mexico', 'code' => 'NM', 'has_income_tax' => true],
            ['name' => 'New York', 'code' => 'NY', 'has_income_tax' => true],
            ['name' => 'North Carolina', 'code' => 'NC', 'has_income_tax' => true],
            ['name' => 'North Dakota', 'code' => 'ND', 'has_income_tax' => true],
            ['name' => 'Ohio', 'code' => 'OH', 'has_income_tax' => true],
            ['name' => 'Oklahoma', 'code' => 'OK', 'has_income_tax' => true],
            ['name' => 'Oregon', 'code' => 'OR', 'has_income_tax' => true],
            ['name' => 'Pennsylvania', 'code' => 'PA', 'has_income_tax' => true],
            ['name' => 'Rhode Island', 'code' => 'RI', 'has_income_tax' => true],
            ['name' => 'South Carolina', 'code' => 'SC', 'has_income_tax' => true],
            ['name' => 'South Dakota', 'code' => 'SD', 'has_income_tax' => false],
            ['name' => 'Tennessee', 'code' => 'TN', 'has_income_tax' => false],
            ['name' => 'Texas', 'code' => 'TX', 'has_income_tax' => false],
            ['name' => 'Utah', 'code' => 'UT', 'has_income_tax' => true],
            ['name' => 'Vermont', 'code' => 'VT', 'has_income_tax' => true],
            ['name' => 'Virginia', 'code' => 'VA', 'has_income_tax' => true],
            ['name' => 'Washington', 'code' => 'WA', 'has_income_tax' => false],
            ['name' => 'West Virginia', 'code' => 'WV', 'has_income_tax' => true],
            ['name' => 'Wisconsin', 'code' => 'WI', 'has_income_tax' => true],
            ['name' => 'Wyoming', 'code' => 'WY', 'has_income_tax' => false],
        ];

        $records = [];
        $now = now();
        foreach ($states as $state) {
            $records[] = [
                'country_id'     => $usCountryId,
                'name'           => $state['name'],
                'code'           => $state['code'],
                'has_income_tax' => $state['has_income_tax'],
                'is_active'      => true,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        DB::table('states')->insert($records);
    }
}
