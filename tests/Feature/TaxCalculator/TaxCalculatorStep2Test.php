<?php

/**
 * Step 2 Service Tests — TaxCalculatorService::calculateTaxesFromSession
 *
 * The anonymous flow no longer writes to the DB.
 * These tests verify that calculateTaxesFromSession produces correct results
 * directly from raw period arrays, mirroring what the controller does.
 */

use App\Models\Country;
use App\Models\TaxType;
use App\Models\TaxBracket;
use App\Services\TaxCalculator\TaxCalculatorService;

beforeEach(function () {
    $this->seed(\Database\Seeders\TaxTypeSeeder::class);
    $this->service    = app(TaxCalculatorService::class);
    $this->incomeTax  = TaxType::where('key', 'income_tax')->first();
    $this->country    = Country::factory()->flatTax(15)->create();
    $this->countryB   = Country::factory()->create(['tax_residency_days' => 183]);
});

// ─── calculateTaxesFromSession ────────────────────────────────────────────────

it('returns key result fields from session data', function () {
    $step1 = [
        'citizenship_country_id'   => $this->country->id,
        'citizenship_country_code' => $this->country->iso_code,
        'annual_income'            => 100000,
        'currency'                 => 'USD',
        'tax_year'                 => 2026,
        'domicile_state_id'        => null,
    ];

    $periods = [
        ['country_id' => $this->country->id, 'days' => 365, 'selected_tax_types' => [], 'local_income' => null],
    ];

    $result = $this->service->calculateTaxesFromSession($step1, $periods);

    expect($result)->toHaveKeys([
        'annual_income', 'currency', 'total_tax', 'net_income',
        'effective_tax_rate', 'breakdown_by_country',
    ]);
});

it('calculates correct flat tax amount from session', function () {
    $step1 = [
        'citizenship_country_id'   => $this->country->id,
        'citizenship_country_code' => $this->country->iso_code,
        'annual_income'            => 100000,
        'currency'                 => 'USD',
        'tax_year'                 => 2026,
        'domicile_state_id'        => null,
    ];

    // 365 days → resident → full income allocated → 15% flat = 15000
    $periods = [
        ['country_id' => $this->country->id, 'days' => 365, 'selected_tax_types' => [], 'local_income' => null],
    ];

    $result = $this->service->calculateTaxesFromSession($step1, $periods);

    expect(round($result['total_tax'], 2))->toBe(15000.0);
    expect(round($result['net_income'], 2))->toBe(85000.0);
});

it('correctly identifies tax resident vs non-resident periods', function () {
    $step1 = [
        'citizenship_country_id'   => $this->country->id,
        'citizenship_country_code' => $this->country->iso_code,
        'annual_income'            => 100000,
        'currency'                 => 'USD',
        'tax_year'                 => 2026,
        'domicile_state_id'        => null,
    ];

    // country: 200 days → above 183 threshold → resident
    // countryB: 165 days → below 183 threshold → non-resident
    $periods = [
        ['country_id' => $this->country->id,  'days' => 200, 'selected_tax_types' => [], 'local_income' => null],
        ['country_id' => $this->countryB->id, 'days' => 165, 'selected_tax_types' => [], 'local_income' => null],
    ];

    $result = $this->service->calculateTaxesFromSession($step1, $periods);

    $residentData = $result['residency_data'];
    $mainResident = collect($residentData)->firstWhere('country_id', $this->country->id);
    $bNonResident = collect($residentData)->firstWhere('country_id', $this->countryB->id);

    expect($mainResident['is_tax_resident'])->toBeTrue();
    expect($bNonResident['is_tax_resident'])->toBeFalse();
});

it('does not write anything to the database during calculation', function () {
    $step1 = [
        'citizenship_country_id'   => $this->country->id,
        'citizenship_country_code' => $this->country->iso_code,
        'annual_income'            => 80000,
        'currency'                 => 'USD',
        'tax_year'                 => 2026,
        'domicile_state_id'        => null,
    ];

    $periods = [
        ['country_id' => $this->country->id, 'days' => 365, 'selected_tax_types' => [], 'local_income' => null],
    ];

    $this->service->calculateTaxesFromSession($step1, $periods);

    expect(\App\Models\UserCalculation::count())->toBe(0);
    expect(\App\Models\UserCalculationCountry::count())->toBe(0);
});

// ─── Territorial income (local_income passthrough) ────────────────────────────

it('uses local_income for territorial countries instead of days-based allocation', function () {
    $territorial = Country::factory()->create([
        'tax_basis'           => 'territorial',
        'tax_residency_days'  => 1,
        'has_progressive_tax' => false,
        'flat_tax_rate'       => 10,
    ]);

    TaxBracket::create([
        'country_id'  => $territorial->id,
        'tax_type_id' => $this->incomeTax->id,
        'min_income'  => 0,
        'rate'        => 10,
        'tax_year'    => 2026,
        'is_active'   => true,
    ]);

    $step1 = [
        'citizenship_country_id'   => $territorial->id,
        'citizenship_country_code' => $territorial->iso_code,
        'annual_income'            => 100000,
        'currency'                 => 'USD',
        'tax_year'                 => 2026,
        'domicile_state_id'        => null,
    ];

    // 200 days in territorial country, earned $30k locally
    $periods = [
        ['country_id' => $territorial->id, 'days' => 365, 'selected_tax_types' => [], 'local_income' => 30000],
    ];

    $result = $this->service->calculateTaxesFromSession($step1, $periods);

    // allocated_income should be $30k, not 100k
    $bd = $result['breakdown_by_country'][0];
    expect($bd['allocated_income'])->toBe(30000.0);
    // 10% flat → 3000
    expect($bd['tax_due'])->toBe(3000.0);
});
