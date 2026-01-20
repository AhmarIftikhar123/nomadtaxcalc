<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app_name',
                'value' => 'Tax Nomad Calculator',
                'type' => 'string',
                'description' => 'Application name',
                'group' => 'app',
                'is_editable' => true,
            ],
            [
                'key' => 'app_version',
                'value' => '1.0.0',
                'type' => 'string',
                'description' => 'Current application version',
                'group' => 'app',
                'is_editable' => false,
            ],
            [
                'key' => 'contact_email',
                'value' => 'support@taxtool.com',
                'type' => 'string',
                'description' => 'Contact email for support',
                'group' => 'app',
                'is_editable' => true,
            ],
            [
                'key' => 'default_currency',
                'value' => 'USD',
                'type' => 'string',
                'description' => 'Default currency for calculations',
                'group' => 'app',
                'is_editable' => true,
            ],
            [
                'key' => 'tax_data_last_updated',
                'value' => '2026-01-17',
                'type' => 'string',
                'description' => 'Last date tax data was updated',
                'group' => 'tax',
                'is_editable' => false,
            ],
            [
                'key' => 'enable_analytics',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable page view analytics',
                'group' => 'app',
                'is_editable' => true,
            ],
            [
                'key' => 'featured_countries_count',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Number of countries to display on homepage',
                'group' => 'app',
                'is_editable' => true,
            ],
            [
                'key' => 'gdpr_compliant',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'GDPR compliance enabled',
                'group' => 'app',
                'is_editable' => false,
            ],
        ];

        DB::table('settings')->insert($settings);
    }
}
