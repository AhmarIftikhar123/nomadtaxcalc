<?php

namespace App\Services\TaxCalculator;

class TaxCalculatorService
{
    /**
     * Get list of countries for selection
     */
    public function getCountries(): array
    {
        return [
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
            'AU' => 'Australia',
            'DE' => 'Germany',
            'FR' => 'France',
            'JP' => 'Japan',
            'SG' => 'Singapore',
            'NZ' => 'New Zealand',
            'CH' => 'Switzerland',
            'NL' => 'Netherlands',
            'SE' => 'Sweden',
            'AE' => 'United Arab Emirates',
            'HK' => 'Hong Kong',
            'MX' => 'Mexico',
            'BR' => 'Brazil',
            'IN' => 'India',
            'TH' => 'Thailand',
            'PT' => 'Portugal',
            'ES' => 'Spain',
        ];
    }

    /**
     * Get list of supported currencies
     */
    public function getCurrencies(): array
    {
        return [
            ['code' => 'USD', 'name' => 'US Dollar ($)', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro (€)', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound (£)', 'symbol' => '£'],
            ['code' => 'CAD', 'name' => 'Canadian Dollar (C$)', 'symbol' => 'C$'],
            ['code' => 'AUD', 'name' => 'Australian Dollar (A$)', 'symbol' => 'A$'],
            ['code' => 'CHF', 'name' => 'Swiss Franc (CHF)', 'symbol' => 'CHF'],
            ['code' => 'SGD', 'name' => 'Singapore Dollar (S$)', 'symbol' => 'S$'],
            ['code' => 'HKD', 'name' => 'Hong Kong Dollar (HK$)', 'symbol' => 'HK$'],
            ['code' => 'JPY', 'name' => 'Japanese Yen (¥)', 'symbol' => '¥'],
        ];
    }
}
