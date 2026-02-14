<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            TaxTypeSeeder::class,
            SettingSeeder::class,
            TaxBracketSeeder::class,
            TaxTreatySeeder::class,
        ]);
    }
}
