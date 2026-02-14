<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // FEIE settings for 2026 (US Foreign Earned Income Exclusion)
            ['key' => 'feie_amount_2026', 'value' => '126500', 'type' => 'integer', 'description' => '2026 Foreign Earned Income Exclusion limit (USD)'],
            ['key' => 'feie_min_days', 'value' => '330', 'type' => 'integer', 'description' => 'Minimum days outside US to qualify for FEIE'],
            
            // Tax year configuration
            ['key' => 'tax_year_current', 'value' => '2026', 'type' => 'integer', 'description' => 'Current tax year for calculations'],
            
            // Global settings
            ['key' => 'default_tax_residency_days', 'value' => '183', 'type' => 'integer', 'description' => 'Default tax residency threshold (days)'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
