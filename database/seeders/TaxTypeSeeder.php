<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['key' => 'income_tax',          'name' => 'Income Tax',                       'description' => 'Standard personal income tax applied to earned income.',                    'is_default' => true,  'is_active' => true, 'sort_order' => 1],
            ['key' => 'social_security',     'name' => 'Social Security / National Insurance', 'description' => 'Social contributions for healthcare, pension, and welfare.',              'is_default' => false, 'is_active' => true, 'sort_order' => 2],
            ['key' => 'municipal_tax',       'name' => 'Municipal / Local Tax',             'description' => 'Additional tax levied by local municipalities or cantons.',                 'is_default' => false, 'is_active' => true, 'sort_order' => 3],
            ['key' => 'capital_gains',       'name' => 'Capital Gains Tax',                 'description' => 'Tax on profit from the sale of assets such as stocks or property.',        'is_default' => false, 'is_active' => true, 'sort_order' => 4],
            ['key' => 'solidarity_surcharge','name' => 'Solidarity Surcharge',              'description' => 'Additional levy applied on top of income tax in certain countries.',        'is_default' => false, 'is_active' => true, 'sort_order' => 5],
        ];

        DB::table('tax_types')->insert($types);
    }
}
