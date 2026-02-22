<?php

namespace Tests\Feature\TaxCalculator;

use App\Models\Country;
use App\Models\TaxType;
use App\Models\TaxBracket;
use App\Models\User;
use App\Models\UserCalculation;
use App\Services\TaxCalculator\TaxCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\TaxTypeSeeder::class);
    $this->service    = app(TaxCalculatorService::class);
    $this->incomeTax  = TaxType::where('key', 'income_tax')->first();
});

// ─── Helper ───────────────────────────────────────────────────────────────────

/**
 * Build a minimal step1 payload as the service expects.
 */
function step1(Country $country, float $income = 120000, string $currency = 'USD', int $year = 2026): array
{
    return [
        'citizenship_country_id'   => $country->id,
        'citizenship_country_code' => $country->iso_code,
        'annual_income'            => $income,
        'currency'                 => $currency,
        'tax_year'                 => $year,
        'domicile_state_id'        => null,
    ];
}

function period(Country $country, int $days, ?float $localIncome = null): array
{
    return [
        'country_id'         => $country->id,
        'days'               => $days,
        'selected_tax_types' => [],
        'local_income'       => $localIncome,
    ];
}

// ─── Scenario 1: US Citizen Digital Nomad in Portugal and Spain ───────────────

test('Scenario 1: US Citizen Digital Nomad in Portugal (NHR) and Spain', function () {
    $us = Country::factory()->us()->create();
    $pt = Country::factory()->create(['iso_code' => 'PT', 'name' => 'Portugal', 'tax_residency_days' => 183]);
    $es = Country::factory()->create(['iso_code' => 'ES', 'name' => 'Spain',    'tax_residency_days' => 183]);

    // US Brackets
    TaxBracket::create(['country_id' => $us->id, 'tax_type_id' => $this->incomeTax->id, 'min_income' => 0,     'max_income' => 10000, 'rate' => 10, 'tax_year' => 2026, 'is_active' => true]);
    TaxBracket::create(['country_id' => $us->id, 'tax_type_id' => $this->incomeTax->id, 'min_income' => 10000,                         'rate' => 20, 'tax_year' => 2026, 'is_active' => true]);

    // Portugal Brackets
    TaxBracket::create(['country_id' => $pt->id, 'tax_type_id' => $this->incomeTax->id, 'min_income' => 0, 'rate' => 20, 'tax_year' => 2026, 'is_active' => true]);

    // Spain Brackets
    TaxBracket::create(['country_id' => $es->id, 'tax_type_id' => $this->incomeTax->id, 'min_income' => 0, 'rate' => 24, 'tax_year' => 2026, 'is_active' => true]);

    // 200 days PT (resident), 100 days ES (non-resident), 65 days US
    $result = $this->service->calculateTaxesFromSession(
        step1($us, 120000),
        [period($pt, 200), period($es, 100), period($us, 65)]
    );

    expect($result)->toHaveKey('total_tax');
    expect($result)->toHaveKey('net_income');
    expect($result['breakdown_by_country'])->toBeArray();

    // PT should appear in breakdown (resident), ES should not (non-resident)
    $countryCodes = collect($result['breakdown_by_country'])->pluck('country_code')->toArray();
    expect(in_array('PT', $countryCodes))->toBeTrue();
    expect(in_array('ES', $countryCodes))->toBeFalse();

    // No DB records created (session-only flow)
    expect(UserCalculation::count())->toBe(0);
});

// ─── Scenario 2: UK Resident High Income ──────────────────────────────────────

test('Scenario 2: UK Resident High Income', function () {
    $uk = Country::factory()->create(['iso_code' => 'GB', 'name' => 'United Kingdom', 'tax_residency_days' => 183]);

    TaxBracket::create(['country_id' => $uk->id, 'tax_type_id' => $this->incomeTax->id, 'min_income' => 0,      'max_income' => 12570,  'rate' => 0,  'tax_year' => 2026, 'is_active' => true]);
    TaxBracket::create(['country_id' => $uk->id, 'tax_type_id' => $this->incomeTax->id, 'min_income' => 12570,  'max_income' => 50270,  'rate' => 20, 'tax_year' => 2026, 'is_active' => true]);
    TaxBracket::create(['country_id' => $uk->id, 'tax_type_id' => $this->incomeTax->id, 'min_income' => 50270,  'max_income' => 125140, 'rate' => 40, 'tax_year' => 2026, 'is_active' => true]);
    TaxBracket::create(['country_id' => $uk->id, 'tax_type_id' => $this->incomeTax->id, 'min_income' => 125140,                          'rate' => 45, 'tax_year' => 2026, 'is_active' => true]);

    $result = $this->service->calculateTaxesFromSession(
        step1($uk, 200000, 'GBP'),
        [period($uk, 365)]
    );

    // High earner: effective rate should be 30–45%
    expect($result['effective_tax_rate'])->toBeGreaterThan(30.0);
    expect($result['effective_tax_rate'])->toBeLessThan(45.0);

    $ukBreakdown = $result['breakdown_by_country'][0];
    expect($ukBreakdown['tax_type_breakdown'][0]['details'])->toContain('Progressive brackets');
});

// ─── Scenario 3: Zero Tax Digital Nomad (UAE territorial) ────────────────────

test('Scenario 3: Zero Tax in UAE when no local income specified', function () {
    $fr  = Country::factory()->create(['iso_code' => 'FR', 'name' => 'France']);
    $uae = Country::factory()->create([
        'iso_code'           => 'AE',
        'name'               => 'United Arab Emirates',
        'tax_residency_days' => 183,
        'tax_basis'          => 'territorial',
        'has_progressive_tax'=> false,
        'flat_tax_rate'      => null,
    ]);

    $result = $this->service->calculateTaxesFromSession(
        step1($fr, 150000, 'EUR'),
        [period($uae, 365, 0)] // 0 local income → $0 taxable
    );

    expect($result['total_tax'])->toEqual(0.0);
    expect($result['effective_tax_rate'])->toEqual(0.0);
    expect($result['net_income'])->toEqual(150000.0);
});

// ─── Scenario 4: Auth Save to DB ─────────────────────────────────────────────

test('Scenario 4: Auth user can save a completed calculation to the DB', function () {
    $user    = User::factory()->create();
    $country = Country::factory()->flatTax(10)->create();

    $step1 = step1($country, 100000);
    $periods = [period($country, 365)];
    $result  = $this->service->calculateTaxesFromSession($step1, $periods);

    $saved = $this->service->saveCalculationForUser($user->id, $step1, $periods, $result);

    expect($saved)->toBeInstanceOf(UserCalculation::class);
    expect($saved->user_id)->toBe($user->id);
    expect(UserCalculation::count())->toBe(1);
});
