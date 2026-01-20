<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxResidencyRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            // Portugal
            [
                'country_id' => 1,
                'rule_type' => 'days_in_country',
                'required_days' => 183,
                'description' => 'Physically present in Portugal for 183+ days in a calendar year',
                'exceptions' => 'Days interrupted by brief trips abroad count. Partial days count as full days.',
                'effective_from' => 2026,
                'is_primary_rule' => true,
                'rule_order' => 1,
            ],
            [
                'country_id' => 1,
                'rule_type' => 'center_of_vital_interests',
                'required_days' => null,
                'description' => 'Permanent home, family, center of personal/economic interests in Portugal',
                'exceptions' => 'Can be considered even without 183 days if center of interests established',
                'effective_from' => 2026,
                'is_primary_rule' => false,
                'rule_order' => 2,
            ],

            // Spain
            [
                'country_id' => 2,
                'rule_type' => 'days_in_country',
                'required_days' => 183,
                'description' => 'More than 183 days physically present in Spain',
                'exceptions' => 'Partial days count. Tax authority can assess other indicators if<183 days.',
                'effective_from' => 2026,
                'is_primary_rule' => true,
                'rule_order' => 1,
            ],

            // US
            [
                'country_id' => 3,
                'rule_type' => 'nationality',
                'required_days' => null,
                'description' => 'US Citizens and Green Card holders taxed on worldwide income regardless of residence',
                'exceptions' => 'FEIE (Foreign Earned Income Exclusion) available for foreign-earned income.',
                'effective_from' => 2026,
                'is_primary_rule' => true,
                'rule_order' => 1,
            ],

            // UK
            [
                'country_id' => 4,
                'rule_type' => 'days_in_country',
                'required_days' => 183,
                'description' => 'UK Statutory Residence Test (SRT) - multiple tests determine residency',
                'exceptions' => 'Complex rules based on work, accommodation, family ties.',
                'effective_from' => 2026,
                'is_primary_rule' => true,
                'rule_order' => 1,
            ],

            // Germany
            [
                'country_id' => 5,
                'rule_type' => 'days_in_country',
                'required_days' => 183,
                'description' => '183 days rule in Germany or any EU/EEA country',
                'exceptions' => 'Center of economic interests can override if clearly in Germany',
                'effective_from' => 2026,
                'is_primary_rule' => true,
                'rule_order' => 1,
            ],

            // France
            [
                'country_id' => 6,
                'rule_type' => 'days_in_country',
                'required_days' => 183,
                'description' => 'Presence in France for 183+ days (continuous or not)',
                'exceptions' => 'Even one day can establish residency if center of interests in France',
                'effective_from' => 2026,
                'is_primary_rule' => true,
                'rule_order' => 1,
            ],

            // Thailand
            [
                'country_id' => 7,
                'rule_type' => 'days_in_country',
                'required_days' => 180,
                'description' => '180 days in Thailand during a calendar year',
                'exceptions' => 'Does not require consecutive days.',
                'effective_from' => 2026,
                'is_primary_rule' => true,
                'rule_order' => 1,
            ],

            // Mexico
            [
                'country_id' => 8,
                'rule_type' => 'days_in_country',
                'required_days' => 183,
                'description' => '183+ days in Mexico in a calendar year',
                'exceptions' => 'Can be established through immigration status or investment.',
                'effective_from' => 2026,
                'is_primary_rule' => true,
                'rule_order' => 1,
            ],

            // UAE
            [
                'country_id' => 9,
                'rule_type' => 'days_in_country',
                'required_days' => 183,
                'description' => 'UAE resident if staying 183+ days in calendar year',
                'exceptions' => 'No personal income tax regardless of residency status',
                'effective_from' => 2026,
                'is_primary_rule' => true,
                'rule_order' => 1,
            ],

            // Singapore
            [
                'country_id' => 10,
                'rule_type' => 'days_in_country',
                'required_days' => 183,
                'description' => 'Singapore resident if present 183+ days in a year',
                'exceptions' => 'Only Singapore-source income is taxed; foreign income is tax-free',
                'effective_from' => 2026,
                'is_primary_rule' => true,
                'rule_order' => 1,
            ],
        ];

        DB::table('tax_residency_rules')->insert($rules);
    }
}
