<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            CountryDataPatchSeeder::class,
            StateSeeder::class,
            TaxTypeSeeder::class,
            SettingSeeder::class,
            TaxBracketSeeder::class,
            TaxBracketSeedar2025::class,
            UsStateBracketSeeder::class,
            TaxTreatySeeder::class,
            DeductionSeeder::class,
            SocialSecurityRuleSeeder::class,
        ]);
    }
}
