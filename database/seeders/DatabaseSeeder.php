<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            TaxBracketSeeder::class,
            TaxResidencyRuleSeeder::class,
            TaxTreatySeeder::class,
            DigitalNomadVisaSeeder::class,
            BlogPostSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
