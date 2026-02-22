<?php

/**
 * Step 3 (Full Pipeline) Tests — TaxCalculatorService::calculateTaxesFromSession
 *
 * These tests verify orchestration of the full tax pipeline from session arrays,
 * replacing the old calculateTaxes(UserCalculation) approach.
 */

use App\Models\Country;
use App\Models\TaxType;
use App\Services\TaxCalculator\TaxCalculatorService;

beforeEach(function () {
    $this->service = app(TaxCalculatorService::class);
    $this->seed(\Database\Seeders\TaxTypeSeeder::class);
    $this->incomeTax = TaxType::where('key', 'income_tax')->first();
    $this->country   = Country::factory()->flatTax(15)->create();
});

/**
 * Helper: build a minimal step1 payload.
 */
function makeStep1(Country $c, float $income = 100000, string $currency = 'USD'): array
{
    return [
        'citizenship_country_id'   => $c->id,
        'citizenship_country_code' => $c->iso_code,
        'annual_income'            => $income,
        'currency'                 => $currency,
        'tax_year'                 => 2026,
        'domicile_state_id'        => null,
    ];
}

/**
 * Helper: build a single residency period array.
 */
function makePeriod(Country $c, int $days, ?float $localIncome = null): array
{
    return ['country_id' => $c->id, 'days' => $days, 'selected_tax_types' => [], 'local_income' => $localIncome];
}

// ─── Result Structure ──────────────────────────────────────────────────────────

it('returns a result array with all required keys', function () {
    $result = $this->service->calculateTaxesFromSession(
        makeStep1($this->country),
        [makePeriod($this->country, 365)]
    );

    expect($result)->toHaveKeys([
        'annual_income', 'currency', 'total_tax', 'net_income',
        'effective_tax_rate', 'breakdown_by_country',
    ]);
});

it('calculates correct totals for a flat tax country', function () {
    $result = $this->service->calculateTaxesFromSession(
        makeStep1($this->country),
        [makePeriod($this->country, 365)]
    );

    // 15% flat tax on full year = 15000
    expect(round($result['total_tax'], 2))->toBe(15000.0);
    expect(round($result['net_income'], 2))->toBe(85000.0);
    expect(round($result['effective_tax_rate'], 2))->toBe(15.0);
});

it('only includes tax-resident countries in breakdown', function () {
    $nonResidentCountry = Country::factory()->flatTax(25)->create();

    $result = $this->service->calculateTaxesFromSession(
        makeStep1($this->country),
        [
            makePeriod($this->country, 265),         // above 183 → resident
            makePeriod($nonResidentCountry, 100),    // below 183 → non-resident
        ]
    );

    // Only resident country shows in breakdown
    expect($result['breakdown_by_country'])->toHaveCount(1);
    expect($result['breakdown_by_country'][0]['country_id'])->toBe($this->country->id);
});

it('returns recommendations array', function () {
    $result = $this->service->calculateTaxesFromSession(
        makeStep1($this->country),
        [makePeriod($this->country, 365)]
    );

    expect($result)->toHaveKey('recommendations');
    expect($result['recommendations'])->toBeArray();
});
