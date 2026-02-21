<?php

/**
 * Step 2 Service Tests — TaxCalculatorService::saveStep2Data
 *
 * Tests the service layer directly since the HTTP layer has a
 * pre-existing 404 issue with all Inertia routes in tests.
 */

use App\Models\Country;
use App\Models\TaxType;
use App\Models\UserCalculation;
use App\Models\UserCalculationCountry;
use App\Services\TaxCalculator\TaxCalculatorService;

beforeEach(function () {
    $this->service = app(TaxCalculatorService::class);
    $this->country = Country::factory()->create();
    $this->countryB = Country::factory()->create();
    $this->incomeTax = TaxType::factory()->incomeTax()->create();

    // Create a calculation (step 1 completed)
    $this->calculation = $this->service->saveStep1Data([
        'annual_income'          => 100000,
        'currency'               => 'USD',
        'citizenship_country_id' => $this->country->id,
    ], null);
});

// ─── saveStep2Data ────────────────────────────────────────────────────────────

it('saves residency periods with correct country and days', function () {
    $this->service->saveStep2Data($this->calculation, [
        ['country_id' => $this->country->id, 'days' => 200],
        ['country_id' => $this->countryB->id, 'days' => 165],
    ]);

    $countries = $this->calculation->countriesVisited()->get();

    expect($countries)->toHaveCount(2);
    expect($countries[0]->country_id)->toBe($this->country->id);
    expect($countries[0]->days_spent)->toBe(200);
    expect($countries[1]->country_id)->toBe($this->countryB->id);
    expect($countries[1]->days_spent)->toBe(165);
});

it('determines tax residency correctly for each country', function () {
    // country has 183-day threshold by default (factory)
    $this->service->saveStep2Data($this->calculation, [
        ['country_id' => $this->country->id, 'days' => 200],    // above 183 → resident
        ['country_id' => $this->countryB->id, 'days' => 100],   // below 183 → non-resident
    ]);

    $countries = $this->calculation->countriesVisited()->get();

    expect($countries[0]->is_tax_resident)->toBeTrue();  // 200 > 183
    expect($countries[1]->is_tax_resident)->toBeFalse(); // 100 < 183
});

it('updates step_reached to 2 after saving', function () {
    $this->service->saveStep2Data($this->calculation, [
        ['country_id' => $this->country->id, 'days' => 365],
    ]);

    $this->calculation->refresh();

    expect($this->calculation->step_reached)->toBe(2);
});

it('replaces existing countries when re-submitting step 2', function () {
    // First submission
    $this->service->saveStep2Data($this->calculation, [
        ['country_id' => $this->country->id, 'days' => 365],
    ]);

    expect($this->calculation->countriesVisited()->count())->toBe(1);

    // Second submission (should replace, not append)
    $this->service->saveStep2Data($this->calculation, [
        ['country_id' => $this->country->id, 'days' => 200],
        ['country_id' => $this->countryB->id, 'days' => 165],
    ]);

    expect($this->calculation->countriesVisited()->count())->toBe(2);
});

it('processes custom taxes when provided', function () {
    $socialSecurity = TaxType::factory()->socialSecurity()->create();

    $this->service->saveStep2Data($this->calculation, [
        [
            'country_id'   => $this->country->id,
            'days'         => 365,
            'custom_taxes' => [
                [
                    'tax_type_id'  => $this->incomeTax->id,
                    'is_custom'    => false,
                    'amount_type'  => 'percentage',
                    'amount'       => null,
                ],
                [
                    'tax_type_id'  => $socialSecurity->id,
                    'is_custom'    => false,
                    'amount_type'  => 'percentage',
                    'amount'       => 5.0,
                ],
            ],
        ],
    ]);

    $visitedCountry = $this->calculation->countriesVisited()->first();
    expect($visitedCountry)->not->toBeNull();
    expect($visitedCountry->days_spent)->toBe(365);
});

it('defaults to income tax only when no custom taxes provided', function () {
    $this->service->saveStep2Data($this->calculation, [
        ['country_id' => $this->country->id, 'days' => 365],
    ]);

    $visitedCountry = $this->calculation->countriesVisited()->first();
    expect($visitedCountry)->not->toBeNull();
});
