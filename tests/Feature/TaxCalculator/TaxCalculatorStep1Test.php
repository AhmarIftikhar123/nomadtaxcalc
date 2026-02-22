<?php

/**
 * Step 1 Service Tests — TaxCalculatorService::buildSessionStep1Payload
 *
 * The anonymous flow no longer writes to the DB.
 * These tests verify the session payload is built correctly.
 */

use App\Models\Country;
use App\Models\UserCalculation;
use App\Models\User;
use App\Services\TaxCalculator\TaxCalculatorService;

beforeEach(function () {
    $this->service = app(TaxCalculatorService::class);
    $this->country = Country::factory()->create();
});

// ─── buildSessionStep1Payload ──────────────────────────────────────────────────

it('builds a correct session payload from step 1 input', function () {
    $payload = $this->service->buildSessionStep1Payload([
        'annual_income'          => 100000,
        'currency'               => 'USD',
        'tax_year'               => 2026,
        'citizenship_country_id' => $this->country->id,
    ]);

    expect($payload)->toMatchArray([
        'annual_income'            => 100000.0,
        'currency'                 => 'USD',
        'tax_year'                 => 2026,
        'citizenship_country_id'   => $this->country->id,
        'citizenship_country_code' => $this->country->iso_code,
    ]);
});

it('does NOT write any record to the database', function () {
    $this->service->buildSessionStep1Payload([
        'annual_income'          => 50000,
        'currency'               => 'EUR',
        'tax_year'               => 2026,
        'citizenship_country_id' => $this->country->id,
    ]);

    expect(UserCalculation::count())->toBe(0);
});

it('includes domicile_state_id when provided', function () {
    $payload = $this->service->buildSessionStep1Payload([
        'annual_income'          => 80000,
        'currency'               => 'USD',
        'tax_year'               => 2026,
        'citizenship_country_id' => $this->country->id,
        'domicile_state_id'      => 5,
    ]);

    expect($payload['domicile_state_id'])->toBe(5);
});

it('defaults domicile_state_id to null when omitted', function () {
    $payload = $this->service->buildSessionStep1Payload([
        'annual_income'          => 80000,
        'currency'               => 'USD',
        'tax_year'               => 2026,
        'citizenship_country_id' => $this->country->id,
    ]);

    expect($payload['domicile_state_id'])->toBeNull();
});

// ─── saveCalculationForUser (auth-gated DB save) ──────────────────────────────

it('creates a UserCalculation record for an authenticated user', function () {
    $user    = User::factory()->create();
    $country = Country::factory()->create();

    $step1 = [
        'citizenship_country_id'   => $country->id,
        'citizenship_country_code' => $country->iso_code,
        'annual_income'            => 120000,
        'currency'                 => 'USD',
        'tax_year'                 => 2026,
        'domicile_state_id'        => null,
    ];

    $result = [
        'annual_income'      => 120000,
        'currency'           => 'USD',
        'tax_year'           => 2026,
        'total_tax'          => 18000,
        'net_income'         => 102000,
        'effective_tax_rate' => 15.0,
        'breakdown_by_country' => [],
        'residency_warnings' => [],
        'treaties_applied'   => [],
        'feie_result'        => null,
    ];

    $calculation = $this->service->saveCalculationForUser($user->id, $step1, [], $result);

    expect($calculation)->toBeInstanceOf(UserCalculation::class);
    expect($calculation->user_id)->toBe($user->id);
    expect($calculation->gross_income)->toEqual(120000);

    $this->assertDatabaseHas('user_calculations', [
        'user_id'      => $user->id,
        'gross_income' => 120000,
        'currency'     => 'USD',
    ]);
});

// ─── getCountries / getCurrencies ─────────────────────────────────────────────

it('retrieves active countries', function () {
    Country::factory()->count(2)->create(['is_active' => true]);
    Country::factory()->create(['is_active' => false]);

    $countries = $this->service->getCountries();

    // 1 from beforeEach + 2 active = 3 (inactive excluded)
    expect($countries->count())->toBe(3);
});

it('includes tax_basis in country list', function () {
    $countries = $this->service->getCountries();
    $first = $countries->first();

    expect($first)->toHaveKey('tax_basis');
});

it('retrieves currencies list', function () {
    $currencies = $this->service->getCurrencies();

    expect($currencies)->toBeArray();
    expect(count($currencies))->toBeGreaterThan(0);
});
