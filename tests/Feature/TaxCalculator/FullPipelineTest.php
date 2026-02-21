<?php

use App\Models\Country;
use App\Models\Setting;
use App\Models\TaxBracket;
use App\Models\TaxTreaty;
use App\Models\TaxType;
use App\Models\UserCalculation;
use App\Models\UserCalculationCountry;
use App\Services\TaxCalculator\TaxCalculatorService;

beforeEach(function () {
    $this->service = app(TaxCalculatorService::class);
    $this->incomeTax = TaxType::factory()->incomeTax()->create();
});

// ─── End-to-End Pipeline Tests ────────────────────────────────────────────────

it('calculates tax for single country with flat tax', function () {
    $country = Country::factory()->flatTax(20)->create();

    $calculation = UserCalculation::factory()->step2Completed()->create([
        'country_id'               => $country->id,
        'citizenship_country_code' => $country->iso_code,
        'gross_income'             => 100000,
        'currency'                 => 'USD',
    ]);

    UserCalculationCountry::factory()->create([
        'user_calculation_id'   => $calculation->id,
        'country_id'            => $country->id,
        'days_spent'            => 365,
        'is_tax_resident'       => true,
    ]);

    $result = $this->service->calculateTaxes($calculation);

    expect($result['annual_income'])->toBe(100000.0);
    expect(round($result['total_tax'], 2))->toBe(20000.0);
    expect(round($result['net_income'], 2))->toBe(80000.0);
    expect(round($result['effective_tax_rate'], 2))->toBe(20.0);
    expect($result['breakdown_by_country'])->toHaveCount(1);
});

it('calculates tax for two countries with proportional income allocation', function () {
    $countryA = Country::factory()->flatTax(10)->create();
    $countryB = Country::factory()->flatTax(30)->create();

    $calculation = UserCalculation::factory()->step2Completed()->create([
        'country_id'               => $countryA->id,
        'citizenship_country_code' => $countryA->iso_code,
        'gross_income'             => 100000,
        'currency'                 => 'USD',
    ]);

    UserCalculationCountry::factory()->create([
        'user_calculation_id'   => $calculation->id,
        'country_id'            => $countryA->id,
        'days_spent'            => 200,
        'is_tax_resident'       => true,
    ]);

    UserCalculationCountry::factory()->create([
        'user_calculation_id'   => $calculation->id,
        'country_id'            => $countryB->id,
        'days_spent'            => 165,
        'is_tax_resident'       => false,
    ]);

    $result = $this->service->calculateTaxes($calculation);

    // Only countryA is tax resident
    $allocatedA = round(100000 / 365 * 200, 2);
    $expectedTax = round($allocatedA * 0.10, 2);

    expect($result['breakdown_by_country'])->toHaveCount(1);
    expect(round($result['total_tax'], 2))->toBe($expectedTax);
});

it('only taxes resident countries, skips non-residents', function () {
    $residentCountry = Country::factory()->flatTax(15)->create(['tax_residency_days' => 183]);
    $nonResidentCountry = Country::factory()->flatTax(25)->create(['tax_residency_days' => 183]);

    $calculation = UserCalculation::factory()->step2Completed()->create([
        'country_id'               => $residentCountry->id,
        'citizenship_country_code' => $residentCountry->iso_code,
        'gross_income'             => 100000,
        'currency'                 => 'USD',
    ]);

    UserCalculationCountry::factory()->create([
        'user_calculation_id'   => $calculation->id,
        'country_id'            => $residentCountry->id,
        'days_spent'            => 265,
        'is_tax_resident'       => true,
    ]);

    UserCalculationCountry::factory()->nonResident(100)->create([
        'user_calculation_id'   => $calculation->id,
        'country_id'            => $nonResidentCountry->id,
    ]);

    $result = $this->service->calculateTaxes($calculation);

    expect($result['breakdown_by_country'])->toHaveCount(1);
    expect($result['breakdown_by_country'][0]['country_id'])->toBe($residentCountry->id);
});

it('adjusts US tax when FEIE eligible', function () {
    $usCountry = Country::factory()->us()->flatTax(20)->create();
    $otherCountry = Country::factory()->flatTax(10)->create();

    Setting::factory()->feieAmount(126500)->create();
    Setting::factory()->feieMinDays(330)->create();

    $calculation = UserCalculation::factory()->step2Completed()->create([
        'country_id'               => $usCountry->id,
        'citizenship_country_code' => $usCountry->iso_code,
        'gross_income'             => 100000,
        'currency'                 => 'USD',
    ]);

    UserCalculationCountry::factory()->nonResident(25)->create([
        'user_calculation_id'   => $calculation->id,
        'country_id'            => $usCountry->id,
    ]);

    UserCalculationCountry::factory()->create([
        'user_calculation_id'   => $calculation->id,
        'country_id'            => $otherCountry->id,
        'days_spent'            => 340,
        'is_tax_resident'       => true,
    ]);

    $result = $this->service->calculateTaxes($calculation);

    expect($result['feie_result'])->not->toBeNull();
    expect($result['feie_result']['eligible'])->toBeTrue();
});

it('applies treaty credit between citizenship and residence country', function () {
    $citizenshipCountry = Country::factory()->create();
    $residenceCountry = Country::factory()->flatTax(20)->create();

    TaxTreaty::factory()->credit()->create([
        'country_a_id'        => $citizenshipCountry->id,
        'country_b_id'        => $residenceCountry->id,
        'applicable_tax_year' => 2026,
    ]);

    $calculation = UserCalculation::factory()->step2Completed()->create([
        'country_id'               => $citizenshipCountry->id,
        'citizenship_country_code' => $citizenshipCountry->iso_code,
        'gross_income'             => 100000,
        'currency'                 => 'USD',
    ]);

    UserCalculationCountry::factory()->create([
        'user_calculation_id'   => $calculation->id,
        'country_id'            => $residenceCountry->id,
        'days_spent'            => 365,
        'is_tax_resident'       => true,
    ]);

    $result = $this->service->calculateTaxes($calculation);

    // Under true FTC, since home tax is 0 (or not resident), total tax is the full foreign tax
    expect(round($result['total_tax'], 2))->toBe(20000.0);
    expect($result['treaties_applied'])->toHaveCount(1);
});
