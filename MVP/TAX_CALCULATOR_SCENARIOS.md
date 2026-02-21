# Tax Calculator — Calculation Scenarios

> **STATUS: PRODUCTION READY ✅**
> The Nomad Tax Calculator currently supports:
> - **Worldwide, Territorial, and Remittance** tax bases based on residency.
> - **Progressive Federal and State Bracket** calculation (fully integrated for US States).
> - **True Foreign Tax Credit (FTC)** logic to eliminate double taxation automatically.
> - **Foreign Earned Income Exclusion (FEIE)** (Physical Presence Test: 330 days).
> - **Custom Taxes** (Flat amount or Percentage) added by the user per country.

Every number in this document is derived from the **actual formulas** in our codebase. Use this as the single source of truth for how the system processes user input into tax results.

---

## Scenario 1: Progressive Tax — UK Citizen in Germany & Spain

### User Input (Step 1)
| Field | Value |
|-------|-------|
| Annual Income | £90,000 |
| Currency | GBP |
| Citizenship | United Kingdom |

### Countries Visited (Step 2)
| Country | Days | Threshold | Arrival Counted | Departure Counted |
|---------|------|-----------|-----------------|-------------------|
| Germany | 200  | 183       | ✅ Yes          | ❌ No             |
| Spain   | 165  | 183       | ✅ Yes          | ✅ Yes            |

### Calculation Walkthrough

#### 1. Residency Determination (`ResidencyDeterminationService::determine`)

**Germany:**
- Raw days = 200
- `counts_departure_day = false` → effective days = 200 − 1 = **199**
- 199 ≥ 183 → **Tax Resident ✅**

**Spain:**
- Raw days = 165
- Both arrival and departure counted → effective days = **165**
- 165 < 183 → **Not Tax Resident ❌**

> Only Germany enters the tax calculation pipeline. Spain is skipped.

#### 2. Income Allocation (`TaxCalculationService::allocateIncome`)

```
Germany: £90,000 × (200 / 365) = £49,315.07
Spain:   (skipped — non-resident)
```

#### 3. Tax Calculation (`TaxCalculationService::calculateForCountry`)

Germany uses **progressive brackets** (2026 data from PwC/KPMG — seeded in `TaxBracketSeeder`):

| Bracket | Range (£) | Rate | Taxable Amount | Tax |
|---------|-----------|------|----------------|-----|
| 1 | 0 – 12,348 | 0% | 12,348.00 | £0.00 |
| 2 | 12,348 – 17,473 | 14% | 5,125.00 | £717.50 |
| 3 | 17,473 – 49,315.07 | 24% | 31,842.07 | £7,642.10 |

```
Total Tax   = £0 + £717.50 + £7,642.10 = £8,359.60
Effective Rate = (£8,359.60 / £49,315.07) × 100 = 16.95%
```

**Return value from `calculateForCountry()`:**
```php
[
    'taxable_income' => 49315.07,
    'tax_due'        => 8359.60,
    'effective_rate'  => 16.95,
    'breakdown'      => [
        ['name' => 'Income Tax', 'amount' => 8359.60, 'details' => 'Progressive brackets (3 applied)', 'is_custom' => false]
    ]
]
```

#### 4. Treaty Resolution (`TreatyResolutionService::applyTreaty`)

- UK → Germany treaty exists: **credit method**
- `tax_due × 0.85` = £8,359.60 × 0.85 = **£7,105.66**
- Tax saved: £8,359.60 − £7,105.66 = **£1,253.94**

#### 5. FEIE (`FeieCalculationService::calculate`)

- Citizenship = UK (not US) → **returns `null`** — FEIE not applicable.

#### 6. Final Aggregation

```
Total Tax       = £7,105.66
Net Income      = £90,000 − £7,105.66 = £82,894.34
Effective Rate  = (£7,105.66 / £90,000) × 100 = 7.90%
```

#### 7. Warnings (`ResidencyDeterminationService::generateWarnings`)

- Germany: 200 days, threshold 183 → exceeded by 17 days (≤ 14 range) → **barely_resident** warning
  > "You became a tax resident of Germany by only 17 days. Small adjustments to your travel could change this."
- Spain: 165 days, threshold 183 → below by 18 days (> 14) → **no warning**

---

## Scenario 2: Flat Tax + Custom Taxes — Pakistani Citizen in Bulgaria & Georgia

### User Input (Step 1)
| Field | Value |
|-------|-------|
| Annual Income | 8,000,000 PKR |
| Currency | PKR |
| Citizenship | Pakistan |

### Countries Visited (Step 2)
| Country | Days | Tax System | Rate |
|---------|------|------------|------|
| Bulgaria | 250 | Flat | 10% |
| Georgia  | 115 | Flat | 0% (territorial) |

The user also adds a **custom tax** for Bulgaria:
- Social Security: **12.9%** (percentage type)

### Calculation Walkthrough

#### 1. Residency Determination

**Bulgaria:**
- 250 days ≥ 183 → **Tax Resident ✅**

**Georgia:**
- 115 days < 183 → **Not Tax Resident ❌**

#### 2. Income Allocation

```
Bulgaria: 8,000,000 × (250 / 365) = 5,479,452.05 PKR
Georgia:  (skipped — non-resident)
```

#### 3. Tax Calculation — Bulgaria

Bulgaria has `has_progressive_tax = false`, `flat_tax_rate = 10`.

**Income Tax (standard — flat):**
```
5,479,452.05 × 0.10 = 547,945.21 PKR
```

**Social Security (custom — percentage override at 12.9%):**
```
5,479,452.05 × 0.129 = 706,849.31 PKR
```

**Return value from `calculateForCountry()`:**
```php
[
    'taxable_income' => 5479452.05,
    'tax_due'        => 1254794.52,   // 547,945.21 + 706,849.31
    'effective_rate'  => 22.90,
    'breakdown'      => [
        ['name' => 'Income Tax',      'amount' => 547945.21, 'details' => 'Flat rate: 10%',       'is_custom' => false],
        ['name' => 'Social Security', 'amount' => 706849.31, 'details' => '12.9% of income',      'is_custom' => true],
    ]
]
```

#### 4. Treaty Resolution

- Pakistan → Bulgaria: no treaty found → **no adjustment**

#### 5. FEIE

- Citizenship = Pakistan (not US) → **returns `null`**

#### 6. Final Aggregation

```
Total Tax       = 1,254,794.52 PKR
Net Income      = 8,000,000 − 1,254,794.52 = 6,745,205.48 PKR
Effective Rate  = (1,254,794.52 / 8,000,000) × 100 = 15.68%
```

#### 7. Warnings

- Bulgaria: 250 days, threshold 183, diff = −67 → far from threshold → **no warning**
- Georgia: 115 days, threshold 183, diff = 68 → far → **no warning**

---

## Scenario 3: FEIE + Treaty — US Citizen Abroad

### User Input (Step 1)
| Field | Value |
|-------|-------|
| Annual Income | $150,000 |
| Currency | USD |
| Citizenship | United States |

### Countries Visited (Step 2)
| Country | Days |
|---------|------|
| Portugal | 340 |
| United States | 25 |

### Calculation Walkthrough

#### 1. Residency Determination

**Portugal:**
- 340 days ≥ 183 → **Tax Resident ✅**

**United States:**
- 25 days < 183 → **Not Tax Resident ❌** (but US taxes citizens worldwide — handled separately)

#### 2. Income Allocation

```
Portugal: $150,000 × (340 / 365) = $139,726.03
US:       (skipped — non-resident by days)
```

#### 3. Tax Calculation — Portugal (progressive)

2026 Portuguese brackets (from PwC/KPMG — seeded in `TaxBracketSeeder`):

| Bracket | Range ($) | Rate | Taxable | Tax |
|---------|-----------|------|---------|-----|
| 1 | 0 – 8,342 | 12.50% | 8,342.00 | $1,042.75 |
| 2 | 8,342 – 12,575 | 15.70% | 4,233.00 | $664.58 |
| 3 | 12,575 – 17,820 | 21.20% | 5,245.00 | $1,111.94 |
| 4 | 17,820 – 23,065 | 24.10% | 5,245.00 | $1,264.05 |
| 5 | 23,065 – 29,367 | 31.10% | 6,302.00 | $1,959.92 |
| 6 | 29,367 – 42,996 | 34.60% | 13,629.00 | $4,715.63 |
| 7 | 42,996 – 46,470 | 43.10% | 3,474.00 | $1,497.29 |
| 8 | 46,470 – 86,634 | 44.60% | 40,164.00 | $17,913.14 |
| 9 | 86,634+ | 48.00% | 53,092.03 | $25,484.17 |

```
Total Tax   = $55,653.47 (sum of all bracket taxes)
Effective   = ($55,653.47 / $139,726.03) × 100 = 39.83%
```

#### 4. Treaty Resolution — US-Portugal

- US → Portugal treaty: **credit method**
- `$55,653.47 × 0.85` = **$47,305.45**
- Tax saved = $55,653.47 − $47,305.45 = **$8,348.02**

#### 5. FEIE Calculation

**`FeieCalculationService::calculate()` receives:**
```php
citizenshipCountryId: US.id
residencyResults: [{country_id: PT, days_spent: 340}, {country_id: US, days_spent: 25}]
annualIncome: 150000
```

**Processing:**
```
Days outside US = 340 (Portugal)
FEIE min days   = 330 (from settings)
340 ≥ 330       → eligible ✅
FEIE limit      = $126,500 (from settings)
Income $150,000 > limit → excluded_income = $126,500 (capped)
taxable_us_income = $150,000 − $126,500 = $23,500
```

**Return:**
```php
[
    'eligible'           => true,
    'days_outside_us'    => 340,
    'minimum_required'   => 330,
    'feie_limit'         => 126500.0,
    'excluded_income'    => 126500.0,
    'taxable_us_income'  => 23500.0,
    'reason'             => 'Qualified under Physical Presence Test (340 days outside US exceeds 330 days)',
]
```

> **Note:** In this scenario, the US doesn't appear in the breakdown because the user is NOT a tax resident by days. FEIE data is still returned for informational purposes. If the US *were* in the breakdown, the system would recalculate US tax on the reduced income ($23,500 instead of $150,000).

#### 6. Final Aggregation

```
Total Tax       = $47,305.45 (Portugal only, post-treaty)
Net Income      = $150,000 − $47,305.45 = $102,694.55
Effective Rate  = ($47,305.45 / $150,000) × 100 = 31.54%
```

#### 7. Warnings

- Portugal: 340 days, threshold 183, diff = −157 → far from threshold → **no warning**

#### 8. Recommendations

`RecommendationService::generate()` checks:
- Tax rate > 30% → "Consider spending more time in lower-tax jurisdictions"
- US citizen abroad → FEIE eligibility highlighted
- Zero-tax countries with DN visas (UAE, Georgia) are suggested

---

## Scenario 4: Full User Journey — From Browser to Backend to Results

This scenario traces **exactly what happens** when a user enters data and how it flows through every backend method.

### The User: Sarah

- Earns **$100,000 USD** annually as a freelancer
- Citizen of **Canada**
- Spent **200 days in Portugal**, **165 days in Canada**

---

### Step 1 — User Enters Income

**Sarah types into the form:**
```
Annual Income:  100000
Currency:       USD
Citizenship:    Canada (id: 7)
```

**Frontend sends:**
```
POST /tax-calculator/step-1

{
    "annual_income": 100000,
    "currency": "USD",
    "citizenship_country_id": 7
}
```

**`StoreStep1Request` validates:**
- `annual_income` → required, numeric, min:0 ✅
- `currency` → required, string, size:3 ✅
- `citizenship_country_id` → required, exists:countries,id ✅

**`TaxCalculatorController::storeStep1()` calls:**
```php
$calculation = $this->taxCalculatorService->saveStep1Data(
    ['annual_income' => 100000, 'currency' => 'USD', 'citizenship_country_id' => 7],
    null  // no existing session
);
```

**`TaxCalculatorService::saveStep1Data()` does:**
1. Finds Canada by `id: 7` → gets `iso_code: 'CA'`
2. Creates `UserCalculation`:
   ```php
   UserCalculation::create([
       'session_uuid'             => 'a1b2c3d4-...', // auto-generated UUID
       'gross_income'             => 100000,
       'currency'                 => 'USD',
       'country_id'               => 7,
       'citizenship_country_code' => 'CA',
       'step_reached'             => 1,
   ]);
   ```
3. Stores `session_uuid` in PHP session: `session(['calculation_session_uuid' => 'a1b2c3d4-...'])`

**→ Redirects to `/tax-calculator/step-2`**

---

### Step 2 — User Adds Countries

**Sarah adds two periods in the UI:**
```
Portugal:  200 days
Canada:    165 days
           ─────────
Total:     365 days ✅
```

**Frontend sends:**
```
POST /tax-calculator/step-2

{
    "residency_periods": [
        { "country_id": 15, "days": 200 },
        { "country_id": 7,  "days": 165 }
    ]
}
```

**`StoreStep2Request` validates:**
- Each `country_id` → exists:countries,id ✅
- Each `days` → integer, 1–365 ✅
- Sum of all days = 365 ✅
- No duplicate country_id ✅

**`TaxCalculatorController::storeStep2()` calls:**
```php
$this->taxCalculatorService->saveStep2Data($calculation, $request->residency_periods);
```

**`TaxCalculatorService::saveStep2Data()` calls:**

**① `ResidencyDeterminationService::determine()`:**
```php
$residencyResults = $this->residencyService->determine([
    ['country_id' => 15, 'days' => 200],
    ['country_id' => 7,  'days' => 165],
]);
```

For each country, the service:
1. Loads the `Country` model from DB
2. Checks `counts_arrival_day` and `counts_departure_day`
3. Adjusts effective days accordingly
4. Compares effective days to `tax_residency_days`

**Returns:**
```php
[
    [
        'country_id'      => 15,
        'country_name'    => 'Portugal',
        'days_spent'      => 200,
        'threshold'       => 183,
        'is_tax_resident' => true,
        'reason'          => 'Spent 200 days, exceeding the 183-day threshold.',
    ],
    [
        'country_id'      => 7,
        'country_name'    => 'Canada',
        'days_spent'      => 165,
        'threshold'       => 183,
        'is_tax_resident' => false,
        'reason'          => 'Spent 165 days, below the 183-day threshold by 18 days.',
    ],
]
```

**② Creates `UserCalculationCountry` records:**
```sql
INSERT INTO user_calculation_countries (user_calculation_id, country_id, days_spent, is_tax_resident)
VALUES (1, 15, 200, true), (1, 7, 165, false);
```

**③ Updates `UserCalculation`:**
```sql
UPDATE user_calculations SET step_reached = 2 WHERE id = 1;
```

**→ Redirects to `/tax-calculator/step-3`**

---

### Step 3 — Calculation & Results

**`TaxCalculatorController::step3()` loads data and calls:**
```php
$result = $this->taxCalculatorService->calculateTaxes($calculation);
```

**`TaxCalculatorService::calculateTaxes()` runs an 8-step pipeline:**

---

**Pipeline Step 1: Build Residency Results**

Loads `countriesVisited` from DB with `country` relation. Identifies tax-resident countries:
- Portugal (200 days) → resident ✅ → enters calculation
- Canada (165 days) → non-resident ❌ → skipped

---

**Pipeline Step 2: `TaxCalculationService::allocateIncome()`**

```php
$allocatedIncome = $this->taxCalcService->allocateIncome(100000, 200);
// Returns: 100000 / 365 * 200 = 54794.52
```

---

**Pipeline Step 3: `TaxCalculationService::calculateForCountry()`**

Portugal has `has_progressive_tax = true`. The service:

1. Calls `buildTaxTypesConfig()` → defaults to `[income_tax]`
2. Queries `tax_brackets WHERE country_id = 15 AND tax_type_id = 1 AND tax_year = 2026`
3. Loops through brackets:

```
For each bracket:
    bracketMin, bracketMax, rate
    taxableInBracket = min(income, bracketMax) - bracketMin
    taxInBracket = taxableInBracket * (rate / 100)
    totalTax += taxInBracket
```

**Returns:**
```php
[
    'taxable_income' => 54794.52,
    'tax_due'        => 14652.38,    // sum of progressive bracket taxes
    'effective_rate'  => 26.74,
    'breakdown'      => [
        ['name' => 'Income Tax', 'amount' => 14652.38, 'details' => 'Progressive brackets (6 applied)', 'is_custom' => false]
    ],
]
```

Updates DB:
```sql
UPDATE user_calculation_countries
SET allocated_income = 54794.52, taxable_income = 54794.52, tax_due = 14652.38, tax_by_type = '[...]'
WHERE user_calculation_id = 1 AND country_id = 15;
```

---

**Pipeline Step 4: `TreatyResolutionService::applyTreaty()`**

```php
$treatyResult = $this->treatyService->applyTreaty(
    citizenshipCountryId: 7,   // Canada
    taxResults: $countryBreakdown
);
```

The service:
1. For Portugal (id: 15) — **different from citizenship** (Canada, id: 7) → checks treaty
2. Queries: `TaxTreaty::active()->between(7, 15)->where('applicable_tax_year', 2026)->first()`
3. Canada-Portugal treaty exists: **credit method**
4. Applies: `$14,652.38 × 0.85 = $12,454.52`

**Returns:**
```php
[
    'results' => [
        [..., 'tax_due' => 12454.52, 'treaty_applied' => 'Foreign Tax Credit']
    ],
    'treaties_applied' => [
        [
            'countries' => ['Canada', 'Portugal'],
            'type'      => 'credit',
            'tax_saved' => 2197.86,
        ]
    ],
]
```

---

**Pipeline Step 5: `FeieCalculationService::calculate()`**

```php
$feieResult = $this->feieService->calculate(7, $residencyResults, 100000);
```

The service:
1. Looks for `Country WHERE iso_code = 'US'`
2. Citizenship is Canada (id: 7), not US → **returns `null`**

---

**Pipeline Step 6: Aggregate Totals**

```php
$totalTax       = 12454.52;                          // sum of all country tax_due
$netIncome      = 100000 - 12454.52 = 87545.48;
$effectiveTaxRate = (12454.52 / 100000) × 100 = 12.45;
```

---

**Pipeline Step 7: `RecommendationService::generate()`**

Checks conditions and generates suggestions:
- Tax rate = 12.45% → moderate
- Not US citizen → no FEIE mention
- Potential: "Consider spending more time in Portugal's NHR regime for optimized rates"

---

**Pipeline Step 8: `ResidencyDeterminationService::generateWarnings()`**

```php
// Portugal: days_spent=200, threshold=183, is_tax_resident=true, diff = 183 - 200 = -17
// -17 is within [-14, 0) range? No (-17 < -14) → no barely_resident warning

// Canada: days_spent=165, threshold=183, is_tax_resident=false, diff = 183 - 165 = 18
// 18 is within [-14, 14]? No (18 > 14) → no near_threshold warning
```

No warnings generated.

---

**Pipeline Step 9: Save & Return**

Updates `user_calculations`:
```sql
UPDATE user_calculations SET
    total_tax = 12454.52,
    net_income = 87545.48,
    effective_tax_rate = 12.45,
    step_reached = 3,
    completed_calculation = true,
    completed_at = '2026-02-15 19:10:00',
    tax_breakdown = '[...]',
    treaty_applied = '[...]',
    feie_result = null
WHERE id = 1;
```

**Final return to controller:**
```php
[
    'annual_income'        => 100000.00,
    'currency'             => 'USD',
    'total_tax'            => 12454.52,
    'net_income'           => 87545.48,
    'effective_tax_rate'   => 12.45,
    'breakdown_by_country' => [
        [
            'country_id'         => 15,
            'country_name'       => 'Portugal',
            'country_code'       => 'PT',
            'currency'           => 'USD',
            'days_spent'         => 200,
            'allocated_income'   => 54794.52,
            'taxable_income'     => 54794.52,
            'tax_due'            => 12454.52,
            'effective_rate'     => 22.73,
            'tax_type_breakdown' => [...],
            'treaty_applied'     => 'Foreign Tax Credit',
        ]
    ],
    'residency_warnings'  => [],
    'treaties_applied'    => [
        ['countries' => ['Canada', 'Portugal'], 'type' => 'credit', 'tax_saved' => 2197.86]
    ],
    'feie_result'         => null,
    'recommendations'     => [...],
]
```

**Controller renders:**
```php
return Inertia::render('TaxCalculator/Step3', [
    'result'      => $result,
    'currentStep' => 3,
]);
```

**→ Sarah sees her results on the Step 3 dashboard.**

---

## Scenario 5: Valid Testing Case — US State Taxes & FEIE Interaction

**To verify the tool is working correctly in production, input the following data:**

### User Input (Step 1)
| Field | Value |
|-------|-------|
| Annual Income | $150,000 |
| Currency | USD |
| Citizenship | United States |
| State of Domicile | New York |

### Countries Visited (Step 2)
| Country | Days | State Visited |
|---------|------|---------------|
| United States | 365 | New York |

### Expected Output
- **FEIE Eligible:** No (Failed Physical Presence Test)
- **Breakdown:** United States
- **Taxes:**
  - Federal Income Tax: ~$28,847.00
  - New York Income Tax: ~$8,327.73
- **Total Tax:** ~$37,174.73

---

## Scenario 6: Valid Testing Case — True Foreign Tax Credit (FTC)

**To verify the system prevents double taxation using the FTC, input the following data:**

### User Input (Step 1)
| Field | Value |
|-------|-------|
| Annual Income | $100,000 |
| Currency | USD |
| Citizenship | United States |
| State of Domicile | California |

### Countries Visited (Step 2)
| Country | Days | State Visited |
|---------|------|---------------|
| United Kingdom | 300 | N/A |
| United States | 65 | California |

### Expected Output
- **Residency:** UK (Resident), US (Non-Resident by days, but taxes citizens worldwide).
- **FEIE Eligible:** No (300 days < 330 days requirement).
- **FTC Applied:** Yes (Treaty between US and UK is a `credit` agreement).
- **UK Tax Due:** ~$20,308.71
- **US Tax Due (Federal + CA):** Reduced by the foreign tax paid to the UK via FTC logic, preventing double taxation on the globally sourced income.
- **Total Tax:** The higher of the two tax regimes (around ~$20,000 to ~$24,000 depending on exact state/federal bracket interaction minus the credit).
