<?php

/**
 * Step 3 Service Tests — TaxCalculatorService::calculateTaxes
 *
 * Tests the calculateTaxes orchestration method directly since the HTTP
 * layer has a pre-existing 404 issue with all Inertia routes in tests.
 */

use App\Models\Country;
use App\Models\TaxType;
use App\Models\UserCalculation;
use App\Models\UserCalculationCountry;
use App\Services\TaxCalculator\TaxCalculatorService;

beforeEach(function () {
    $this->service = app(TaxCalculatorService::class);
    $this->incomeTax = TaxType::factory()->incomeTax()->create();
    $this->country = Country::factory()->flatTax(15)->create();
});

// ─── calculateTaxes Result Structure ──────────────────────────────────────────

it('returns a result array with all required keys', function () {
    $calculation = UserCalculation::factory()->step2Completed()->create([
        'country_id'               => $this->country->id,
        'citizenship_country_code' => $this->country->iso_code,
        'gross_income'             => 100000,
        'currency'                 => 'USD',
    ]);

    UserCalculationCountry::factory()->create([
        'user_calculation_id' => $calculation->id,
        'country_id'          => $this->country->id,
        'days_spent'          => 365,
        'is_tax_resident'     => true,
    ]);

    $result = $this->service->calculateTaxes($calculation);

    expect($result)->toHaveKeys([
        'annual_income',
        'currency',
        'total_tax',
        'net_income',
        'effective_tax_rate',
        'breakdown_by_country',
    ]);
});

it('calculates correct totals for a flat tax country', function () {
    $calculation = UserCalculation::factory()->step2Completed()->create([
        'country_id'               => $this->country->id,
        'citizenship_country_code' => $this->country->iso_code,
        'gross_income'             => 100000,
        'currency'                 => 'USD',
    ]);

    UserCalculationCountry::factory()->create([
        'user_calculation_id' => $calculation->id,
        'country_id'          => $this->country->id,
        'days_spent'          => 365,
        'is_tax_resident'     => true,
    ]);

    $result = $this->service->calculateTaxes($calculation);

    // 15% flat tax on full year = 15000
    expect(round($result['total_tax'], 2))->toBe(15000.0);
    expect(round($result['net_income'], 2))->toBe(85000.0);
    expect(round($result['effective_tax_rate'], 2))->toBe(15.0);
});

it('only includes tax-resident countries in breakdown', function () {
    $nonResidentCountry = Country::factory()->flatTax(25)->create();

    $calculation = UserCalculation::factory()->step2Completed()->create([
        'country_id'               => $this->country->id,
        'citizenship_country_code' => $this->country->iso_code,
        'gross_income'             => 100000,
        'currency'                 => 'USD',
    ]);

    UserCalculationCountry::factory()->create([
        'user_calculation_id' => $calculation->id,
        'country_id'          => $this->country->id,
        'days_spent'          => 265,
        'is_tax_resident'     => true,
    ]);

    UserCalculationCountry::factory()->nonResident(100)->create([
        'user_calculation_id' => $calculation->id,
        'country_id'          => $nonResidentCountry->id,
    ]);

    $result = $this->service->calculateTaxes($calculation);

    // Only resident country shows in breakdown
    expect($result['breakdown_by_country'])->toHaveCount(1);
    expect($result['breakdown_by_country'][0]['country_id'])->toBe($this->country->id);
});

it('returns recommendations array', function () {
    $calculation = UserCalculation::factory()->step2Completed()->create([
        'country_id'               => $this->country->id,
        'citizenship_country_code' => $this->country->iso_code,
        'gross_income'             => 100000,
        'currency'                 => 'USD',
    ]);

    UserCalculationCountry::factory()->create([
        'user_calculation_id' => $calculation->id,
        'country_id'          => $this->country->id,
        'days_spent'          => 365,
        'is_tax_resident'     => true,
    ]);

    $result = $this->service->calculateTaxes($calculation);

    expect($result)->toHaveKey('recommendations');
    expect($result['recommendations'])->toBeArray();
});
