<?php

use App\Models\Country;
use App\Models\Setting;
use App\Services\TaxCalculator\FeieCalculationService;

beforeEach(function () {
    $this->service = app(FeieCalculationService::class);

    $this->usCountry = Country::factory()->us()->create();
    $this->otherCountry = Country::factory()->create();

    // Create FEIE settings
    Setting::factory()->feieAmount(126500)->create();
    Setting::factory()->feieMinDays(330)->create();
});

// ─── FEIE Eligibility ─────────────────────────────────────────────────────────

it('returns null for non-US citizens', function () {
    $nonUsCountry = Country::factory()->create();

    $result = $this->service->calculate(
        $nonUsCountry->id,
        [['country_id' => $this->otherCountry->id, 'days_spent' => 365]],
        100000
    );

    expect($result)->toBeNull();
});

it('marks US citizen as eligible when exceeding 330 days outside US', function () {
    $result = $this->service->calculate(
        $this->usCountry->id,
        [
            ['country_id' => $this->usCountry->id,   'days_spent' => 25],
            ['country_id' => $this->otherCountry->id, 'days_spent' => 340],
        ],
        100000
    );

    expect($result['eligible'])->toBeTrue();
    expect($result['days_outside_us'])->toBe(340);
    expect($result['excluded_income'])->toBe(100000.0); // below FEIE limit
});

it('marks US citizen as not eligible when below 330 days outside US', function () {
    $result = $this->service->calculate(
        $this->usCountry->id,
        [
            ['country_id' => $this->usCountry->id,   'days_spent' => 65],
            ['country_id' => $this->otherCountry->id, 'days_spent' => 300],
        ],
        100000
    );

    expect($result['eligible'])->toBeFalse();
    expect($result['excluded_income'])->toBe(0);
});

it('caps excluded income at FEIE limit for high earners', function () {
    $result = $this->service->calculate(
        $this->usCountry->id,
        [
            ['country_id' => $this->otherCountry->id, 'days_spent' => 365],
        ],
        200000 // higher than FEIE limit
    );

    expect($result['eligible'])->toBeTrue();
    expect($result['excluded_income'])->toBe(126500.0); // capped
    expect($result['taxable_us_income'])->toBe(200000.0 - 126500.0);
});

it('excludes full income when below FEIE limit', function () {
    $result = $this->service->calculate(
        $this->usCountry->id,
        [
            ['country_id' => $this->otherCountry->id, 'days_spent' => 365],
        ],
        50000 // below FEIE limit
    );

    expect($result['eligible'])->toBeTrue();
    expect($result['excluded_income'])->toBe(50000.0);
    expect($result['taxable_us_income'])->toEqual(0);
});
