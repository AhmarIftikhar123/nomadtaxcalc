<?php

use App\Models\Country;
use App\Services\TaxCalculator\ResidencyDeterminationService;

beforeEach(function () {
    $this->service = app(ResidencyDeterminationService::class);
});

// ─── Residency Determination ──────────────────────────────────────────────────

it('marks as tax resident when days exceed threshold', function () {
    $country = Country::factory()->create(['tax_residency_days' => 183]);

    $results = $this->service->determine([
        ['country_id' => $country->id, 'days' => 200],
    ]);

    expect($results)->toHaveCount(1);
    expect($results[0]['is_tax_resident'])->toBeTrue();
});

it('marks as non-resident when days are below threshold', function () {
    $country = Country::factory()->create(['tax_residency_days' => 183]);

    $results = $this->service->determine([
        ['country_id' => $country->id, 'days' => 100],
    ]);

    expect($results[0]['is_tax_resident'])->toBeFalse();
});

it('marks as tax resident when days exactly equal threshold', function () {
    $country = Country::factory()->create(['tax_residency_days' => 183]);

    $results = $this->service->determine([
        ['country_id' => $country->id, 'days' => 183],
    ]);

    expect($results[0]['is_tax_resident'])->toBeTrue();
});

it('subtracts one day when country does not count arrival day', function () {
    $country = Country::factory()->noArrivalDay()->create(['tax_residency_days' => 183]);

    // 183 days minus 1 (arrival not counted) = 182 < 183 threshold
    $results = $this->service->determine([
        ['country_id' => $country->id, 'days' => 183],
    ]);

    expect($results[0]['is_tax_resident'])->toBeFalse();
});

it('subtracts one day when country does not count departure day', function () {
    $country = Country::factory()->noDepartureDay()->create(['tax_residency_days' => 183]);

    // 183 days minus 1 (departure not counted) = 182 < 183 threshold
    $results = $this->service->determine([
        ['country_id' => $country->id, 'days' => 183],
    ]);

    expect($results[0]['is_tax_resident'])->toBeFalse();
});

it('subtracts two days when neither arrival nor departure is counted', function () {
    $country = Country::factory()->noArrivalDay()->noDepartureDay()->create([
        'tax_residency_days' => 183,
    ]);

    // 185 minus 2 = 183, exactly at threshold → resident
    $results = $this->service->determine([
        ['country_id' => $country->id, 'days' => 185],
    ]);

    expect($results[0]['is_tax_resident'])->toBeTrue();

    // 184 minus 2 = 182, below threshold → NOT resident
    $results2 = $this->service->determine([
        ['country_id' => $country->id, 'days' => 184],
    ]);

    expect($results2[0]['is_tax_resident'])->toBeFalse();
});

// ─── Warnings ─────────────────────────────────────────────────────────────────

it('generates near-threshold warning', function () {
    $country = Country::factory()->create(['tax_residency_days' => 183]);

    $residencyResults = [
        [
            'country_name' => $country->name,
            'days_spent'   => 175,
            'threshold'    => 183,
            'is_tax_resident' => false,
        ],
    ];

    $warnings = $this->service->generateWarnings($residencyResults);

    expect($warnings)->toHaveCount(1);
    expect($warnings[0]['type'])->toBe('near_threshold');
});

it('generates barely-resident warning', function () {
    $country = Country::factory()->create(['tax_residency_days' => 183]);

    $residencyResults = [
        [
            'country_name' => $country->name,
            'days_spent'   => 190,
            'threshold'    => 183,
            'is_tax_resident' => true,
        ],
    ];

    $warnings = $this->service->generateWarnings($residencyResults);

    expect($warnings)->toHaveCount(1);
    expect($warnings[0]['type'])->toBe('barely_resident');
});

it('generates no warning when far from threshold', function () {
    $residencyResults = [
        [
            'country_name' => 'Test Country',
            'days_spent'   => 50,
            'threshold'    => 183,
            'is_tax_resident' => false,
        ],
    ];

    $warnings = $this->service->generateWarnings($residencyResults);

    expect($warnings)->toBeEmpty();
});
