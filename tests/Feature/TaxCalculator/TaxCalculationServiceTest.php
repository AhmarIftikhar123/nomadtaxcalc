<?php

use App\Models\Country;
use App\Models\TaxBracket;
use App\Models\TaxType;
use App\Services\TaxCalculator\TaxCalculationService;

beforeEach(function () {
    $this->service = app(TaxCalculationService::class);
    $this->incomeTax = TaxType::factory()->incomeTax()->create();
});

// ─── Income Allocation ────────────────────────────────────────────────────────

it('allocates income proportionally based on days spent', function () {
    $country = Country::factory()->create(['tax_basis' => 'worldwide']);
    $result = $this->service->allocateIncome($country, 100000, 200);
    $expected = round(100000 / 365 * 200, 2);

    expect(round($result, 2))->toBe($expected);
});

it('allocates full income for a full year (365 days)', function () {
    $country = Country::factory()->create(['tax_basis' => 'worldwide']);
    $result = $this->service->allocateIncome($country, 100000, 365);

    expect(round($result, 2))->toBe(100000.0);
});

// ─── Flat Tax Calculation ─────────────────────────────────────────────────────

it('calculates flat tax correctly', function () {
    $country = Country::factory()->flatTax(15.0)->create();

    $result = $this->service->calculateForCountry($country, 100000, [
        ['tax_type_id' => $this->incomeTax->id, 'is_custom' => false],
    ]);

    expect(round($result['tax_due'], 2))->toBe(15000.0);
    expect(round($result['effective_rate'], 2))->toBe(15.0);
    expect($result['taxable_income'])->toBe(100000.0);
});

// ─── Progressive Tax (Brackets) Calculation ──────────────────────────────────

it('calculates progressive tax with multiple brackets', function () {
    $country = Country::factory()->create(['has_progressive_tax' => true]);

    // Create 3 brackets: 0-10k@10%, 10k-50k@20%, 50k+@30%
    TaxBracket::factory()->create([
        'country_id'  => $country->id,
        'tax_type_id' => $this->incomeTax->id,
        'min_income'  => 0,
        'max_income'  => 10000,
        'rate'        => 10,
    ]);
    TaxBracket::factory()->create([
        'country_id'  => $country->id,
        'tax_type_id' => $this->incomeTax->id,
        'min_income'  => 10000,
        'max_income'  => 50000,
        'rate'        => 20,
    ]);
    TaxBracket::factory()->create([
        'country_id'  => $country->id,
        'tax_type_id' => $this->incomeTax->id,
        'min_income'  => 50000,
        'max_income'  => null,
        'rate'        => 30,
    ]);

    $result = $this->service->calculateForCountry($country, 80000, [
        ['tax_type_id' => $this->incomeTax->id, 'is_custom' => false],
    ]);

    // Expected: 10000*0.10 + 40000*0.20 + 30000*0.30 = 1000 + 8000 + 9000 = 18000
    expect(round($result['tax_due'], 2))->toBe(18000.0);
    expect($result['breakdown'])->toHaveCount(1);
    expect($result['breakdown'][0]['name'])->toBe('Income Tax');
});

// ─── Custom Tax Types ─────────────────────────────────────────────────────────

it('calculates custom percentage tax type', function () {
    $country = Country::factory()->flatTax(10)->create();

    $result = $this->service->calculateForCountry($country, 100000, [
        ['tax_type_id' => $this->incomeTax->id, 'is_custom' => false],
        [
            'is_custom'    => true,
            'custom_name'  => 'Social Security',
            'amount_type'  => 'percentage',
            'amount'       => 5.0,
        ],
    ]);

    // Income tax: 10% = 10000, Social Security: 5% = 5000 = total 15000
    expect(round($result['tax_due'], 2))->toBe(15000.0);
    expect($result['breakdown'])->toHaveCount(2);
    expect($result['breakdown'][1]['is_custom'])->toBeTrue();
    expect($result['breakdown'][1]['name'])->toBe('Social Security');
});

it('calculates custom flat amount tax type', function () {
    $country = Country::factory()->flatTax(10)->create();

    $result = $this->service->calculateForCountry($country, 100000, [
        ['tax_type_id' => $this->incomeTax->id, 'is_custom' => false],
        [
            'is_custom'    => true,
            'custom_name'  => 'Municipal Tax',
            'amount_type'  => 'flat',
            'amount'       => 1500,
        ],
    ]);

    // Income tax: 10% = 10000, Municipal Tax: flat 1500 = total 11500
    expect(round($result['tax_due'], 2))->toBe(11500.0);
    expect($result['breakdown'][1]['details'])->toBe('Flat annual amount');
});

// ─── Override on Standard Tax Type ────────────────────────────────────────────

it('applies user override on a standard tax type', function () {
    $country = Country::factory()->create();
    $socialSecurity = TaxType::factory()->socialSecurity()->create();

    // Progressive brackets for income tax
    TaxBracket::factory()->create([
        'country_id'  => $country->id,
        'tax_type_id' => $this->incomeTax->id,
        'min_income'  => 0,
        'max_income'  => null,
        'rate'        => 20,
    ]);

    $result = $this->service->calculateForCountry($country, 100000, [
        ['tax_type_id' => $this->incomeTax->id, 'is_custom' => false],
        [
            'tax_type_id'  => $socialSecurity->id,
            'is_custom'    => false,
            'amount_type'  => 'percentage',
            'amount'       => 7.5,
        ],
    ]);

    // Income tax: 20% = 20000, Social Security override: 7.5% = 7500 = total 27500
    expect(round($result['tax_due'], 2))->toBe(27500.0);
    expect($result['breakdown'])->toHaveCount(2);
});

// ─── Edge Cases ───────────────────────────────────────────────────────────────

it('returns zero tax when no brackets exist and no flat rate', function () {
    $country = Country::factory()->create([
        'has_progressive_tax' => true,
        'flat_tax_rate'       => null,
    ]);

    $result = $this->service->calculateForCountry($country, 100000, [
        ['tax_type_id' => $this->incomeTax->id, 'is_custom' => false],
    ]);

    expect($result['tax_due'])->toBe(0.0);
});

it('applies bracket cap correctly', function () {
    $country = Country::factory()->create();

    TaxBracket::factory()->withCap(5000)->create([
        'country_id'  => $country->id,
        'tax_type_id' => $this->incomeTax->id,
        'min_income'  => 0,
        'max_income'  => null,
        'rate'        => 20,
    ]);

    $result = $this->service->calculateForCountry($country, 100000, [
        ['tax_type_id' => $this->incomeTax->id, 'is_custom' => false],
    ]);

    // 20% of 100k = 20000, but capped at 5000
    expect(round($result['tax_due'], 2))->toBe(5000.0);
});

it('returns zero tax and zero effective rate for zero income', function () {
    $country = Country::factory()->flatTax(15)->create();

    $result = $this->service->calculateForCountry($country, 0, [
        ['tax_type_id' => $this->incomeTax->id, 'is_custom' => false],
    ]);

    expect($result['tax_due'])->toBe(0.0);
    expect($result['effective_rate'])->toBe(0.0);
});
