<?php

use App\Models\Country;
use App\Models\TaxTreaty;
use App\Services\TaxCalculator\TreatyResolutionService;

beforeEach(function () {
    $this->service = app(TreatyResolutionService::class);
    $this->countryA = Country::factory()->create(['name' => 'Country A']);
    $this->countryB = Country::factory()->create(['name' => 'Country B']);
});

// ─── Treaty Application ──────────────────────────────────────────────────────

it('makes no adjustment when no treaty exists', function () {
    $taxResults = [
        [
            'country_id'       => $this->countryB->id,
            'country_name'     => 'Country B',
            'tax_due'          => 10000,
            'allocated_income' => 50000,
            'is_tax_resident'  => true,
        ],
    ];

    $result = $this->service->applyTreaty($this->countryA->id, $taxResults);

    expect($result['results'][0]['tax_due'])->toBe(10000);
    expect($result['treaties_applied'])->toBeEmpty();
});

it('applies credit treaty to home country tax', function () {
    TaxTreaty::factory()->credit()->create([
        'country_a_id'        => $this->countryA->id,
        'country_b_id'        => $this->countryB->id,
        'applicable_tax_year' => 2026,
    ]);

    $taxResults = [
        [
            'country_id'       => $this->countryA->id,
            'country_name'     => 'Country A',
            'tax_due'          => 15000,
            'allocated_income' => 50000,
            'is_tax_resident'  => true,
        ],
        [
            'country_id'       => $this->countryB->id,
            'country_name'     => 'Country B',
            'tax_due'          => 10000,
            'allocated_income' => 50000,
            'is_tax_resident'  => false,
        ],
    ];

    $result = $this->service->applyTreaty($this->countryA->id, $taxResults);

    // Credit logic: Foreign tax stays 10000, Home tax becomes max(0, 15000 - 10000) = 5000
    // Result array order might differ, let's look for Country A
    $countryAResult = collect($result['results'])->firstWhere('country_id', $this->countryA->id);
    $countryBResult = collect($result['results'])->firstWhere('country_id', $this->countryB->id);

    expect(round($countryBResult['tax_due'], 2))->toBe(10000.0);
    expect(round($countryAResult['tax_due'], 2))->toBe(5000.0);
    expect($result['treaties_applied'])->toHaveCount(1);
    expect($result['treaties_applied'][0]['type'])->toBe('credit');
    expect($result['treaties_applied'][0]['tax_saved'])->toEqual(10000.0); // Home tax saved 10000
});

it('calculates credit treaty savings when home country is not in residency results', function () {
    TaxTreaty::factory()->credit()->create([
        'country_a_id'        => $this->countryA->id,
        'country_b_id'        => $this->countryB->id,
        'applicable_tax_year' => 2026,
    ]);

    $taxResults = [
        [
            'country_id'       => $this->countryB->id,
            'country_name'     => 'Country B',
            'tax_due'          => 10000,
            'allocated_income' => 50000,
            'is_tax_resident'  => false,
        ],
    ];

    $result = $this->service->applyTreaty($this->countryA->id, $taxResults);

    // Foreign tax stays 10000. Home country isn't in results, but treaty tax_saved should still reflect the paid foreign tax as a credit.
    expect(round($result['results'][0]['tax_due'], 2))->toBe(10000.0);
    expect($result['treaties_applied'])->toHaveCount(1);
    expect($result['treaties_applied'][0]['type'])->toBe('credit');
    expect($result['treaties_applied'][0]['tax_saved'])->toEqual(10000.0);
});

it('applies exemption treaty for non-resident with zero tax', function () {
    TaxTreaty::factory()->exemption()->create([
        'country_a_id'        => $this->countryA->id,
        'country_b_id'        => $this->countryB->id,
        'applicable_tax_year' => 2026,
    ]);

    $taxResults = [
        [
            'country_id'       => $this->countryB->id,
            'country_name'     => 'Country B',
            'tax_due'          => 10000,
            'allocated_income' => 50000,
            'is_tax_resident'  => false,
        ],
    ];

    $result = $this->service->applyTreaty($this->countryA->id, $taxResults);

    expect($result['results'][0]['tax_due'])->toBe(0);
});

it('skips exemption treaty when user is tax resident', function () {
    TaxTreaty::factory()->exemption()->create([
        'country_a_id'        => $this->countryA->id,
        'country_b_id'        => $this->countryB->id,
        'applicable_tax_year' => 2026,
    ]);

    $taxResults = [
        [
            'country_id'       => $this->countryB->id,
            'country_name'     => 'Country B',
            'tax_due'          => 10000,
            'allocated_income' => 50000,
            'is_tax_resident'  => true, // resident — exemption should be skipped
        ],
    ];

    $result = $this->service->applyTreaty($this->countryA->id, $taxResults);

    // Tax stays the same
    expect($result['results'][0]['tax_due'])->toBe(10000);
    expect($result['treaties_applied'])->toBeEmpty();
});

it('applies partial treaty with 50% reduction', function () {
    TaxTreaty::factory()->partial()->create([
        'country_a_id'        => $this->countryA->id,
        'country_b_id'        => $this->countryB->id,
        'applicable_tax_year' => 2026,
    ]);

    $taxResults = [
        [
            'country_id'       => $this->countryB->id,
            'country_name'     => 'Country B',
            'tax_due'          => 10000,
            'allocated_income' => 50000,
            'is_tax_resident'  => false,
        ],
    ];

    $result = $this->service->applyTreaty($this->countryA->id, $taxResults);

    expect(round($result['results'][0]['tax_due'], 2))->toBe(5000.0);
});

it('skips treaty lookup when country is same as citizenship', function () {
    $taxResults = [
        [
            'country_id'       => $this->countryA->id, // same as citizenship
            'country_name'     => 'Country A',
            'tax_due'          => 10000,
            'allocated_income' => 50000,
        ],
    ];

    $result = $this->service->applyTreaty($this->countryA->id, $taxResults);

    expect($result['results'][0]['tax_due'])->toBe(10000);
    expect($result['treaties_applied'])->toBeEmpty();
});
