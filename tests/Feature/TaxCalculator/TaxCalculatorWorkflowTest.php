<?php

namespace Tests\Feature\TaxCalculator;

use App\Models\Country;
use App\Models\TaxType;
use App\Models\TaxBracket;
use App\Models\UserCalculation;
use App\Services\TaxCalculator\TaxCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed Tax Types
    $this->seed(\Database\Seeders\TaxTypeSeeder::class);
    
    // Initialize Service via content
    $this->service = app(TaxCalculatorService::class);
});

test('Scenario 1: US Citizen Digital Nomad in Portugal (NHR) and Spain', function () {
    // 1. Setup Data
    $us = Country::factory()->us()->create();
    $pt = Country::factory()->create(['iso_code' => 'PT', 'name' => 'Portugal', 'tax_residency_days' => 183]);
    $es = Country::factory()->create(['iso_code' => 'ES', 'name' => 'Spain', 'tax_residency_days' => 183]);

    // Setup Brackets similar to seeders
    $incomeTax = TaxType::where('key', 'income_tax')->first();
    
    // US Brackets (Simplified for test) - 10% up to 10k, 20% after
    TaxBracket::create(['country_id' => $us->id, 'tax_type_id' => $incomeTax->id, 'min_income' => 0, 'max_income' => 10000, 'rate' => 10, 'tax_year' => 2026, 'is_active' => true]);
    TaxBracket::create(['country_id' => $us->id, 'tax_type_id' => $incomeTax->id, 'min_income' => 10000, 'rate' => 20, 'tax_year' => 2026, 'is_active' => true]);

    // Portugal Brackets - 20% Flat (NHR simulation for test simplicity)
    TaxBracket::create(['country_id' => $pt->id, 'tax_type_id' => $incomeTax->id, 'min_income' => 0, 'rate' => 20, 'tax_year' => 2026, 'is_active' => true]);

    // Spain Brackets - 24% Flat
    TaxBracket::create(['country_id' => $es->id, 'tax_type_id' => $incomeTax->id, 'min_income' => 0, 'rate' => 24, 'tax_year' => 2026, 'is_active' => true]);

    // 2. Step 1: Save Basic Info
    $calculation = $this->service->saveStep1Data([
        'annual_income' => 120000, // $120k
        'currency' => 'USD',
        'tax_year' => 2026,
        'citizenship_country_id' => $us->id,
    ]);

    // 3. Step 2: Add Residency Periods
    // 200 days in PT (Tax Resident), 100 days in ES (Non-Resident), 65 days in US
    $periods = [
        [
            'country_id' => $pt->id,
            'days' => 200,
            'selected_tax_types' => [], // Default income tax
        ],
        [
            'country_id' => $es->id,
            'days' => 100,
            'selected_tax_types' => [],
        ],
        [
            'country_id' => $us->id,
            'days' => 65,
            'selected_tax_types' => [],
        ]
    ];
    
    $this->service->saveStep2Data($calculation, $periods);
    
    // Refresh to get relations
    $calculation->load('countriesVisited');
    
    // 4. Verify Residency Logic
    $ptVisit = $calculation->countriesVisited->where('country_id', $pt->id)->first();
    $esVisit = $calculation->countriesVisited->where('country_id', $es->id)->first();
    
    expect($ptVisit->is_tax_resident)->toBeTrue();
    expect($esVisit->is_tax_resident)->toBeFalse();

    // 5. Calculate Taxes
    $results = $this->service->calculateTaxes($calculation);

    // 6. Assertions
    // Should have tax due in Portugal (Resident)
    // Should have tax due in US (Citizen, always taxed)
    // Spain (Non-resident) depends on territorial rules, but typically 0 if no local income source defined
    
    // Check results structure
    expect($results)->toHaveKey('total_tax');
    expect($results)->toHaveKey('net_income');
    expect($results['breakdown_by_country'])->toBeArray();

    // Verify FEIE application for US
    $usResult = collect($results['breakdown_by_country'])->firstWhere('country_code', 'US');
    if ($usResult) {
        // FEIE should reduce taxable income
        expect($usResult['feie_applied'])->toBeTrue();
        expect($usResult['feie_exclusion'])->toBeGreaterThan(0);
    }
});

test('Scenario 2: UK Resident High Income', function () {
    // 1. Setup
    $uk = Country::factory()->create(['iso_code' => 'GB', 'name' => 'United Kingdom', 'tax_residency_days' => 183]);
    $incomeTax = TaxType::where('key', 'income_tax')->first();

    // UK Brackets: 0% up to 12.5k, 20% to 50k, 40% to 125k, 45% above
    TaxBracket::create(['country_id' => $uk->id, 'tax_type_id' => $incomeTax->id, 'min_income' => 0, 'max_income' => 12570, 'rate' => 0, 'tax_year' => 2026, 'is_active' => true]);
    TaxBracket::create(['country_id' => $uk->id, 'tax_type_id' => $incomeTax->id, 'min_income' => 12570, 'max_income' => 50270, 'rate' => 20, 'tax_year' => 2026, 'is_active' => true]);
    TaxBracket::create(['country_id' => $uk->id, 'tax_type_id' => $incomeTax->id, 'min_income' => 50270, 'max_income' => 125140, 'rate' => 40, 'tax_year' => 2026, 'is_active' => true]);
    TaxBracket::create(['country_id' => $uk->id, 'tax_type_id' => $incomeTax->id, 'min_income' => 125140, 'rate' => 45, 'tax_year' => 2026, 'is_active' => true]);

    // 2. Step 1
    $calculation = $this->service->saveStep1Data([
        'annual_income' => 200000, // £200k - High earner
        'currency' => 'GBP',
        'tax_year' => 2026,
        'citizenship_country_id' => $uk->id,
    ]);

    // 3. Step 2 - Full year in UK
    $this->service->saveStep2Data($calculation, [
        ['country_id' => $uk->id, 'days' => 365, 'selected_tax_types' => []]
    ]);

    // 4. Calculate
    $results = $this->service->calculateTaxes($calculation);

    // 5. Verify Progressive Tax
    expect($results['effective_tax_rate'])->toBeGreaterThan(30); // Should be high ~35-40%
    expect($results['effective_tax_rate'])->toBeLessThan(45); // But strictly less than top marginal rate
    
    $ukBreakdown = $results['breakdown_by_country'][0];
    // Check breakdown details
    $taxDetails = $ukBreakdown['tax_type_breakdown'][0];
    expect($taxDetails['details'])->toContain('Progressive brackets');
});

test('Scenario 3: Zero Tax Digital Nomad (UAE)', function () {
    // 1. Setup
    $fr = Country::factory()->create(['iso_code' => 'FR', 'name' => 'France']); // Citizenship
    $uae = Country::factory()->create(['iso_code' => 'AE', 'name' => 'United Arab Emirates', 'tax_residency_days' => 183]); // Residence
    
    // UAE has no income tax brackets (empty)
    
    // 2. Step 1
    $calculation = $this->service->saveStep1Data([
        'annual_income' => 150000,
        'currency' => 'EUR',
        'tax_year' => 2026,
        'citizenship_country_id' => $fr->id,
    ]);

    // 3. Step 2 - Full year in UAE
    $this->service->saveStep2Data($calculation, [
        ['country_id' => $uae->id, 'days' => 365, 'selected_tax_types' => []]
    ]);

    // 4. Calculate
    $results = $this->service->calculateTaxes($calculation);

    // 5. Verify 0% Tax
    expect($results['total_tax'])->toEqual(0);
    expect($results['effective_tax_rate'])->toEqual(0);
    expect($results['net_income'])->toEqual(150000);
});
