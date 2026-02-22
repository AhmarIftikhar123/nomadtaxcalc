<?php

use App\Models\Country;
use App\Models\Setting;
use App\Models\TaxBracket;
use App\Models\TaxTreaty;
use App\Models\TaxType;
use App\Models\User;
use App\Models\UserCalculation;
use App\Services\TaxCalculator\TaxCalculatorService;

beforeEach(function () {
    $this->seed(\Database\Seeders\TaxTypeSeeder::class);
    $this->service    = app(TaxCalculatorService::class);
    $this->incomeTax  = TaxType::where('key', 'income_tax')->first();
});

/**
 * Build a minimal step1 payload for a given country.
 */
function pipelineStep1(Country $c, float $income = 100000, string $currency = 'USD', int $year = 2026): array
{
    return [
        'citizenship_country_id'   => $c->id,
        'citizenship_country_code' => $c->iso_code,
        'annual_income'            => $income,
        'currency'                 => $currency,
        'tax_year'                 => $year,
        'domicile_state_id'        => null,
    ];
}

/**
 * Build a residency period entry.
 */
function pipelinePeriod(Country $c, int $days, ?float $localIncome = null): array
{
    return ['country_id' => $c->id, 'days' => $days, 'selected_tax_types' => [], 'local_income' => $localIncome];
}

// ─── Flat Tax ──────────────────────────────────────────────────────────────────

it('calculates tax for single country with flat tax', function () {
    $country = Country::factory()->flatTax(20)->create();

    $result = $this->service->calculateTaxesFromSession(
        pipelineStep1($country),
        [pipelinePeriod($country, 365)]
    );

    expect($result['annual_income'])->toBe(100000.0);
    expect(round($result['total_tax'], 2))->toBe(20000.0);
    expect(round($result['net_income'], 2))->toBe(80000.0);
    expect(round($result['effective_tax_rate'], 2))->toBe(20.0);
    expect($result['breakdown_by_country'])->toHaveCount(1);
});

// ─── Proportional Allocation ───────────────────────────────────────────────────

it('calculates tax for two countries with proportional income allocation', function () {
    $countryA = Country::factory()->flatTax(10)->create();
    $countryB = Country::factory()->flatTax(30)->create();

    $result = $this->service->calculateTaxesFromSession(
        pipelineStep1($countryA),
        [
            pipelinePeriod($countryA, 200), // above 183 → resident
            pipelinePeriod($countryB, 165), // below 183 → non-resident
        ]
    );

    // Only countryA is tax resident → proportional allocation
    $allocatedA  = round(100000 / 365 * 200, 2);
    $expectedTax = round($allocatedA * 0.10, 2);

    expect($result['breakdown_by_country'])->toHaveCount(1);
    expect(round($result['total_tax'], 2))->toBe($expectedTax);
});

// ─── Residency Filter ──────────────────────────────────────────────────────────

it('only taxes resident countries, skips non-residents', function () {
    $residentCountry    = Country::factory()->flatTax(15)->create(['tax_residency_days' => 183]);
    $nonResidentCountry = Country::factory()->flatTax(25)->create(['tax_residency_days' => 183]);

    $result = $this->service->calculateTaxesFromSession(
        pipelineStep1($residentCountry),
        [
            pipelinePeriod($residentCountry, 265),   // above threshold
            pipelinePeriod($nonResidentCountry, 100), // below threshold
        ]
    );

    expect($result['breakdown_by_country'])->toHaveCount(1);
    expect($result['breakdown_by_country'][0]['country_id'])->toBe($residentCountry->id);
});

// ─── FEIE (US Citizen) ─────────────────────────────────────────────────────────

it('adjusts US tax when FEIE eligible', function () {
    $usCountry    = Country::factory()->us()->flatTax(20)->create();
    $otherCountry = Country::factory()->flatTax(10)->create();

    Setting::factory()->feieAmount(126500)->create();
    Setting::factory()->feieMinDays(330)->create();

    $result = $this->service->calculateTaxesFromSession(
        pipelineStep1($usCountry),
        [
            pipelinePeriod($usCountry, 25),      // non-resident in US
            pipelinePeriod($otherCountry, 340),  // resident abroad
        ]
    );

    expect($result['feie_result'])->not->toBeNull();
    expect($result['feie_result']['eligible'])->toBeTrue();
});

// ─── Treaty Credit ─────────────────────────────────────────────────────────────

it('applies treaty credit between citizenship and residence country', function () {
    $citizenshipCountry = Country::factory()->create();
    $residenceCountry   = Country::factory()->flatTax(20)->create();

    TaxTreaty::factory()->credit()->create([
        'country_a_id'        => $citizenshipCountry->id,
        'country_b_id'        => $residenceCountry->id,
        'applicable_tax_year' => 2026,
    ]);

    $result = $this->service->calculateTaxesFromSession(
        pipelineStep1($citizenshipCountry),
        [pipelinePeriod($residenceCountry, 365)]
    );

    // Full foreign tax — home country isn't a resident country
    expect(round($result['total_tax'], 2))->toBe(20000.0);
    expect($result['treaties_applied'])->toHaveCount(1);
});

// ─── Auth Save ─────────────────────────────────────────────────────────────────

it('saves a completed calculation for an authenticated user', function () {
    $user    = User::factory()->create();
    $country = Country::factory()->flatTax(10)->create();

    $step1   = pipelineStep1($country);
    $periods = [pipelinePeriod($country, 365)];
    $result  = $this->service->calculateTaxesFromSession($step1, $periods);

    $saved = $this->service->saveCalculationForUser($user->id, $step1, $periods, $result);

    expect($saved)->toBeInstanceOf(UserCalculation::class);
    expect($saved->user_id)->toBe($user->id);
    expect(UserCalculation::count())->toBe(1);
});
