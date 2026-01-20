<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxBracketSeeder extends Seeder
{
    public function run(): void
    {
        $taxBrackets = [
            // Portugal - 2026 Tax Brackets
            ['country_id' => 1, 'min_income' => 0, 'max_income' => 7479, 'tax_rate' => 14.5, 'effective_year' => 2026],
            ['country_id' => 1, 'min_income' => 7479, 'max_income' => 18894, 'tax_rate' => 23, 'effective_year' => 2026],
            ['country_id' => 1, 'min_income' => 18894, 'max_income' => 45307, 'tax_rate' => 28.5, 'effective_year' => 2026],
            ['country_id' => 1, 'min_income' => 45307, 'max_income' => 80882, 'tax_rate' => 37, 'effective_year' => 2026],
            ['country_id' => 1, 'min_income' => 80882, 'max_income' => null, 'tax_rate' => 45, 'effective_year' => 2026],

            // Spain - 2026 Tax Brackets
            ['country_id' => 2, 'min_income' => 0, 'max_income' => 15000, 'tax_rate' => 19, 'effective_year' => 2026],
            ['country_id' => 2, 'min_income' => 15000, 'max_income' => 43192, 'tax_rate' => 24, 'effective_year' => 2026],
            ['country_id' => 2, 'min_income' => 43192, 'max_income' => 130000, 'tax_rate' => 30, 'effective_year' => 2026],
            ['country_id' => 2, 'min_income' => 130000, 'max_income' => null, 'tax_rate' => 45, 'effective_year' => 2026],

            // US Federal - 2026 Tax Brackets (estimated)
            ['country_id' => 3, 'min_income' => 0, 'max_income' => 11600, 'tax_rate' => 10, 'effective_year' => 2026],
            ['country_id' => 3, 'min_income' => 11600, 'max_income' => 47150, 'tax_rate' => 12, 'effective_year' => 2026],
            ['country_id' => 3, 'min_income' => 47150, 'max_income' => 100525, 'tax_rate' => 22, 'effective_year' => 2026],
            ['country_id' => 3, 'min_income' => 100525, 'max_income' => 191950, 'tax_rate' => 24, 'effective_year' => 2026],
            ['country_id' => 3, 'min_income' => 191950, 'max_income' => 243725, 'tax_rate' => 32, 'effective_year' => 2026],
            ['country_id' => 3, 'min_income' => 243725, 'max_income' => 609350, 'tax_rate' => 35, 'effective_year' => 2026],
            ['country_id' => 3, 'min_income' => 609350, 'max_income' => null, 'tax_rate' => 37, 'effective_year' => 2026],

            // UK - 2026 Tax Brackets
            ['country_id' => 4, 'min_income' => 0, 'max_income' => 12570, 'tax_rate' => 0, 'effective_year' => 2026],
            ['country_id' => 4, 'min_income' => 12570, 'max_income' => 50270, 'tax_rate' => 20, 'effective_year' => 2026],
            ['country_id' => 4, 'min_income' => 50270, 'max_income' => 125140, 'tax_rate' => 40, 'effective_year' => 2026],
            ['country_id' => 4, 'min_income' => 125140, 'max_income' => null, 'tax_rate' => 45, 'effective_year' => 2026],

            // Germany - 2026 Tax Brackets
            ['country_id' => 5, 'min_income' => 0, 'max_income' => 11604, 'tax_rate' => 0, 'effective_year' => 2026],
            ['country_id' => 5, 'min_income' => 11604, 'max_income' => 48009, 'tax_rate' => 19, 'effective_year' => 2026],
            ['country_id' => 5, 'min_income' => 48009, 'max_income' => 88956, 'tax_rate' => 31, 'effective_year' => 2026],
            ['country_id' => 5, 'min_income' => 88956, 'max_income' => 173056, 'tax_rate' => 42, 'effective_year' => 2026],
            ['country_id' => 5, 'min_income' => 173056, 'max_income' => null, 'tax_rate' => 45, 'effective_year' => 2026],

            // France - 2026 Tax Brackets
            ['country_id' => 6, 'min_income' => 0, 'max_income' => 11294, 'tax_rate' => 0, 'effective_year' => 2026],
            ['country_id' => 6, 'min_income' => 11294, 'max_income' => 28797, 'tax_rate' => 11, 'effective_year' => 2026],
            ['country_id' => 6, 'min_income' => 28797, 'max_income' => 82341, 'tax_rate' => 30, 'effective_year' => 2026],
            ['country_id' => 6, 'min_income' => 82341, 'max_income' => 177106, 'tax_rate' => 41, 'effective_year' => 2026],
            ['country_id' => 6, 'min_income' => 177106, 'max_income' => null, 'tax_rate' => 45, 'effective_year' => 2026],

            // Thailand - 2026 Tax Brackets
            ['country_id' => 7, 'min_income' => 0, 'max_income' => 150000, 'tax_rate' => 5, 'effective_year' => 2026],
            ['country_id' => 7, 'min_income' => 150000, 'max_income' => 300000, 'tax_rate' => 10, 'effective_year' => 2026],
            ['country_id' => 7, 'min_income' => 300000, 'max_income' => 500000, 'tax_rate' => 15, 'effective_year' => 2026],
            ['country_id' => 7, 'min_income' => 500000, 'max_income' => 750000, 'tax_rate' => 20, 'effective_year' => 2026],
            ['country_id' => 7, 'min_income' => 750000, 'max_income' => 1000000, 'tax_rate' => 25, 'effective_year' => 2026],
            ['country_id' => 7, 'min_income' => 1000000, 'max_income' => 2000000, 'tax_rate' => 30, 'effective_year' => 2026],
            ['country_id' => 7, 'min_income' => 2000000, 'max_income' => 5000000, 'tax_rate' => 35, 'effective_year' => 2026],
            ['country_id' => 7, 'min_income' => 5000000, 'max_income' => null, 'tax_rate' => 37, 'effective_year' => 2026],

            // Mexico - 2026 Tax Brackets
            ['country_id' => 8, 'min_income' => 0, 'max_income' => 248457, 'tax_rate' => 1.92, 'effective_year' => 2026],
            ['country_id' => 8, 'min_income' => 248457, 'max_income' => 745357, 'tax_rate' => 6.4, 'effective_year' => 2026],
            ['country_id' => 8, 'min_income' => 745357, 'max_income' => 1620000, 'tax_rate' => 10.88, 'effective_year' => 2026],
            ['country_id' => 8, 'min_income' => 1620000, 'max_income' => 2490000, 'tax_rate' => 16, 'effective_year' => 2026],
            ['country_id' => 8, 'min_income' => 2490000, 'max_income' => 3108000, 'tax_rate' => 19.52, 'effective_year' => 2026],
            ['country_id' => 8, 'min_income' => 3108000, 'max_income' => null, 'tax_rate' => 35, 'effective_year' => 2026],

            // UAE - 0% Tax (no brackets needed, but included for completeness)
            ['country_id' => 9, 'min_income' => 0, 'max_income' => null, 'tax_rate' => 0, 'effective_year' => 2026],

            // Singapore - 2026 Tax Brackets
            ['country_id' => 10, 'min_income' => 0, 'max_income' => 20000, 'tax_rate' => 0, 'effective_year' => 2026],
            ['country_id' => 10, 'min_income' => 20000, 'max_income' => 30000, 'tax_rate' => 2, 'effective_year' => 2026],
            ['country_id' => 10, 'min_income' => 30000, 'max_income' => 40000, 'tax_rate' => 3.5, 'effective_year' => 2026],
            ['country_id' => 10, 'min_income' => 40000, 'max_income' => 80000, 'tax_rate' => 7, 'effective_year' => 2026],
            ['country_id' => 10, 'min_income' => 80000, 'max_income' => 120000, 'tax_rate' => 11.5, 'effective_year' => 2026],
            ['country_id' => 10, 'min_income' => 120000, 'max_income' => 160000, 'tax_rate' => 15, 'effective_year' => 2026],
            ['country_id' => 10, 'min_income' => 160000, 'max_income' => 200000, 'tax_rate' => 18, 'effective_year' => 2026],
            ['country_id' => 10, 'min_income' => 200000, 'max_income' => 320000, 'tax_rate' => 19, 'effective_year' => 2026],
            ['country_id' => 10, 'min_income' => 320000, 'max_income' => null, 'tax_rate' => 22, 'effective_year' => 2026],
        ];

        DB::table('tax_brackets')->insert($taxBrackets);
    }
}
