<?php

/**
 * Step 1 Service Tests — TaxCalculatorService::saveStep1Data
 *
 * Tests the service layer directly since the HTTP layer has a
 * pre-existing 404 issue with all Inertia routes in tests.
 */

use App\Models\Country;
use App\Models\UserCalculation;
use App\Services\TaxCalculator\TaxCalculatorService;

beforeEach(function () {
    $this->service = app(TaxCalculatorService::class);
    $this->country = Country::factory()->create();
});

// ─── saveStep1Data ────────────────────────────────────────────────────────────

it('creates a new UserCalculation record on first submission', function () {
    $result = $this->service->saveStep1Data([
        'annual_income'          => 100000,
        'currency'               => 'USD',
        'tax_year'               => 2026,
        'citizenship_country_id' => $this->country->id,
    ], null);

    expect($result)->toBeInstanceOf(UserCalculation::class);
    expect($result->gross_income)->toEqual(100000);
    expect($result->currency)->toBe('USD');
    expect($result->tax_year)->toEqual(2026);
    expect($result->citizenship_country_code)->toBe($this->country->iso_code);

    $this->assertDatabaseHas('user_calculations', [
        'gross_income' => 100000,
        'currency'     => 'USD',
        'tax_year'     => 2026,
    ]);
});

it('updates existing UserCalculation on re-submission with same session', function () {
    $first = $this->service->saveStep1Data([
        'annual_income'          => 100000,
        'currency'               => 'USD',
        'tax_year'               => 2025,
        'citizenship_country_id' => $this->country->id,
    ], null);

    $second = $this->service->saveStep1Data([
        'annual_income'          => 150000,
        'currency'               => 'EUR',
        'tax_year'               => 2026,
        'citizenship_country_id' => $this->country->id,
    ], $first->session_uuid);

    expect(UserCalculation::count())->toBe(1);
    expect($second->gross_income)->toEqual(150000);
    expect($second->currency)->toBe('EUR');
    expect($second->tax_year)->toEqual(2026);
});

it('generates a session UUID automatically', function () {
    $result = $this->service->saveStep1Data([
        'annual_income'          => 50000,
        'currency'               => 'GBP',
        'tax_year'               => 2026,
        'citizenship_country_id' => $this->country->id,
    ], null);

    expect($result->session_uuid)->not->toBeNull();
    expect(strlen($result->session_uuid))->toBe(36); // UUID format
});

// ─── getCountries / getCurrencies ──────────────────────────────────────────────

it('retrieves active countries', function () {
    // Create additional active and inactive countries
    Country::factory()->count(2)->create(['is_active' => true]);
    Country::factory()->create(['is_active' => false]);

    $countries = $this->service->getCountries();

    // 1 from beforeEach + 2 active = 3 (inactive excluded)
    expect($countries->count())->toBe(3);
});

it('retrieves currencies list', function () {
    $currencies = $this->service->getCurrencies();

    expect($currencies)->toBeArray();
    expect(count($currencies))->toBeGreaterThan(0);
});
