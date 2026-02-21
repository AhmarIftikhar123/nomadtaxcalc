# Dynamic Tax Year Implementation Plan

## Overview

Currently the tax year is **hardcoded to `2026`** in backend services and **`2024`** in frontend labels. Since the DB has bracket data for both 2025 and 2026, the year should be user-selectable and dynamic. Future years can be added to the DB and automatically appear in the dropdown.

---

## All Files To Be Modified

| # | File | Type | Change |
|---|------|------|--------|
| 1 | `database/migrations/xxxx_add_tax_year_to_user_calculations.php` | **New** | Add `tax_year` column to `user_calculations` |
| 2 | `app/Models/UserCalculation.php` | Edit | Add `tax_year` to `$fillable` |
| 3 | `app/Http/Requests/TaxCalculator/StoreStep1Request.php` | Edit | Add `tax_year` validation |
| 4 | `app/Http/Controllers/TaxCalculator/TaxCalculatorController.php` | Edit | Query available years, pass to frontend, include `tax_year` in data flow |
| 5 | `app/Services/TaxCalculator/TaxCalculatorService.php` | Edit | Accept + save `tax_year`, pass it through the pipeline |
| 6 | `app/Services/TaxCalculator/TaxCalculationService.php` | Edit | Replace hardcoded `2026` with dynamic `$taxYear` parameter |
| 7 | `app/Services/TaxCalculator/TreatyResolutionService.php` | Edit | Replace hardcoded `2026` with dynamic `$taxYear` parameter |
| 8 | `resources/js/Pages/TaxCalculator/Index.jsx` | Edit | Add `tax_year` to form data, pass `availableYears` to Step1Form |
| 9 | `resources/js/Components/TaxCalculator/Step1Form.jsx` | Edit | Add Year Select dropdown using `Select.jsx` |
| 10 | `resources/js/Pages/TaxCalculator/Step2.jsx` | Edit | Pass `tax_year` to Step2Form |
| 11 | `resources/js/Components/TaxCalculator/Step2Form.jsx` | Edit | Replace hardcoded "2024" with dynamic year |
| 12 | `resources/js/Components/TaxCalculator/Form1Summary.jsx` | Edit | Show selected year in step1 summary |
| 13 | `resources/js/Pages/TaxCalculator/Step3.jsx` | Edit | Display selected year in heading; pass year to DetailedTaxBreakdown |
| 14 | `resources/js/Components/TaxCalculator/DetailedTaxBreakdown.jsx` | Edit | Add Year column to table + CSV export |

---

## Step-by-Step Implementation

### Step 1: Database Migration — Add `tax_year` to `user_calculations`

**File:** `database/migrations/2026_02_16_000001_add_tax_year_to_user_calculations_table.php` (NEW)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_calculations', function (Blueprint $table) {
            $table->year('tax_year')->default(2026)->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('user_calculations', function (Blueprint $table) {
            $table->dropColumn('tax_year');
        });
    }
};
```

**Run:** `php artisan migrate`

---

### Step 2: Update `UserCalculation` Model

**File:** `app/Models/UserCalculation.php`

Add `'tax_year'` to `$fillable`:

```php
protected $fillable = [
    'session_uuid',
    'step_reached',
    'started_at',
    'completed_at',
    'completed_calculation',
    'country_id',
    'ip_address',
    'gross_income',
    'currency',
    'tax_year',          // ← ADD
    'citizenship_country_code',
    // ...rest stays the same
];
```

Add to `$casts`:

```php
protected $casts = [
    // ...existing casts
    'tax_year' => 'integer',   // ← ADD
];
```

---

### Step 3: Update `StoreStep1Request`

**File:** `app/Http/Requests/TaxCalculator/StoreStep1Request.php`

```php
public function rules(): array
{
    return [
        'annual_income'          => 'required|numeric|min:0',
        'currency'               => 'required|string|size:3',
        'citizenship_country_id' => 'required|exists:countries,id',
        'tax_year'               => 'required|integer|min:2020|max:2099',  // ← ADD
    ];
}
```

---

### Step 4: Update `TaxCalculatorController`

**File:** `app/Http/Controllers/TaxCalculator/TaxCalculatorController.php`

#### 4a. `index()` — Query available years from DB and pass to frontend

```php
public function index()
{
    $countries  = $this->taxCalculatorService->getCountries();
    $currencies = $this->taxCalculatorService->getCurrencies();

    // Get distinct tax years from tax_brackets table (dynamic)
    $availableYears = \App\Models\TaxBracket::select('tax_year')
        ->distinct()
        ->where('is_active', true)
        ->orderByDesc('tax_year')
        ->pluck('tax_year')
        ->toArray();

    return Inertia::render('TaxCalculator/Index', [
        'countries'      => $countries,
        'currencies'     => $currencies,
        'availableYears' => $availableYears,     // ← ADD
        'currentStep'    => 1,
    ]);
}
```

#### 4b. `storeStep1()` — Include `tax_year` in saved data

```php
public function storeStep1(StoreStep1Request $request)
{
    $sessionUuid = session('calculation_session_uuid');

    $calculation = $this->taxCalculatorService->saveStep1Data(
        $request->only(['annual_income', 'currency', 'citizenship_country_id', 'tax_year']),  // ← ADD tax_year
        $sessionUuid
    );

    session(['calculation_session_uuid' => $calculation->session_uuid]);

    return redirect()->route('tax-calculator.step-2');
}
```

#### 4c. `step2()` — Pass `tax_year` to frontend via `step1Data`

```php
public function step2()
{
    // ...existing session check code...

    return Inertia::render('TaxCalculator/Step2', [
        'countries' => $countries,
        'taxTypes'  => $taxTypes,
        'step1Data' => [
            'annual_income'              => $calculation->gross_income,
            'currency'                   => $calculation->currency,
            'tax_year'                   => $calculation->tax_year,             // ← ADD
            'citizenship_country_code'   => $calculation->citizenship_country_code,
            'citizenship_country_name'   => $citizenshipCountry?->name ?? 'Unknown',
        ],
        'currentStep' => 2,
    ]);
}
```

#### 4d. `step3()` — Pass `tax_year` in result

No changes needed here — the `calculateTaxes()` method will include `tax_year` in its return array (see Step 6).

---

### Step 5: Update `TaxCalculatorService`

**File:** `app/Services/TaxCalculator/TaxCalculatorService.php`

#### 5a. `saveStep1Data()` — Save `tax_year`

```php
public function saveStep1Data(array $data, ?string $sessionUuid = null): UserCalculation
{
    $country = Country::findOrFail($data['citizenship_country_id']);

    if (!$sessionUuid) {
        $sessionUuid = (string) Str::uuid();
    }

    $calculation = UserCalculation::updateOrCreate(
        ['session_uuid' => $sessionUuid],
        [
            'country_id'               => $country->id,
            'gross_income'              => $data['annual_income'],
            'currency'                  => $data['currency'],
            'tax_year'                  => $data['tax_year'],                  // ← ADD
            'citizenship_country_code'  => $country->iso_code,
            'ip_address'                => request()->ip(),
            'device_type'               => $this->detectDeviceType(),
            'referrer'                  => request()->headers->get('referer'),
            'step_reached'              => 1,
            'started_at'                => now(),
        ]
    );

    return $calculation;
}
```

#### 5b. `calculateTaxes()` — Pass `tax_year` to downstream services

In the `calculateTaxes()` method, extract `$taxYear` from `$calculation` and pass it:

```php
public function calculateTaxes(UserCalculation $calculation): array
{
    $annualIncome = (float) $calculation->gross_income;
    $taxYear = $calculation->tax_year ?? 2026;    // ← ADD (fallback for old records)

    // ...existing code...

    // Step 2: Calculate tax for each resident country
    foreach ($residentCountries as $calcCountry) {
        // ...existing allocation code...

        // Pass $taxYear to calculateForCountry
        $taxResult = $this->taxCalcService->calculateForCountry(
            $country,
            $allocatedIncome,
            $taxTypesConfig,
            $taxYear                               // ← ADD parameter
        );

        // ...existing country breakdown building...
    }

    // Step 3: Apply treaties
    $treatyResult = $this->treatyService->applyTreaty(
        $citizenshipCountryId,
        $countryBreakdown,
        $taxYear                                    // ← ADD parameter
    );

    // ...existing FEIE, aggregation, recommendations code...

    // Step 8: Return — include tax_year
    return [
        'annual_income'        => round($annualIncome, 2),
        'currency'             => $calculation->currency,
        'tax_year'             => $taxYear,          // ← ADD
        'total_tax'            => round($totalTax, 2),
        // ...rest stays the same...
    ];
}
```

---

### Step 6: Update `TaxCalculationService`

**File:** `app/Services/TaxCalculator/TaxCalculationService.php`

#### 6a. `calculateForCountry()` — Accept `$taxYear` parameter

```php
// BEFORE:
public function calculateForCountry(Country $country, float $allocatedIncome, array $taxTypesConfig = []): array

// AFTER:
public function calculateForCountry(Country $country, float $allocatedIncome, array $taxTypesConfig = [], int $taxYear = 2026): array
```

Pass `$taxYear` when calling `calculateBrackets()`:

```php
$bracketResult = $this->calculateBrackets($country, $allocatedIncome, $taxConfig['tax_type_id'], $taxYear);
```

#### 6b. `calculateBrackets()` — Accept and use `$taxYear` parameter

```php
// BEFORE:
private function calculateBrackets(Country $country, float $income, int $taxTypeId): array

// AFTER:
private function calculateBrackets(Country $country, float $income, int $taxTypeId, int $taxYear = 2026): array
{
    $brackets = TaxBracket::where('country_id', $country->id)
        ->where('tax_type_id', $taxTypeId)
        ->where('tax_year', $taxYear)           // ← DYNAMIC
        ->where('is_active', true)
        ->orderBy('min_income')
        ->get();

    // ...rest stays the same
}
```

---

### Step 7: Update `TreatyResolutionService`

**File:** `app/Services/TaxCalculator/TreatyResolutionService.php`

```php
// BEFORE:
public function applyTreaty(int $citizenshipCountryId, array $taxResults): array

// AFTER:
public function applyTreaty(int $citizenshipCountryId, array $taxResults, int $taxYear = 2026): array
{
    // ...inside the loop...
    $treaty = TaxTreaty::active()
        ->between($citizenshipCountryId, $residenceCountryId)
        ->where('applicable_tax_year', $taxYear)    // ← DYNAMIC
        ->first();

    // ...rest stays the same
}
```

---

### Step 8: Frontend — `Index.jsx`

**File:** `resources/js/Pages/TaxCalculator/Index.jsx`

Add `availableYears` to props and `tax_year` to form data:

```jsx
// BEFORE:
export default function TaxCalculatorIndex({ auth, countries, currencies }) {
    const { data, setData, post, processing, errors } = useForm({
        annual_income: "",
        currency: "USD",
        citizenship_country_id: "",
    });

// AFTER:
export default function TaxCalculatorIndex({ auth, countries, currencies, availableYears }) {
    const { data, setData, post, processing, errors } = useForm({
        annual_income: "",
        currency: "USD",
        citizenship_country_id: "",
        tax_year: availableYears?.[0] || new Date().getFullYear(),   // default to latest year
    });
```

Pass `availableYears` to `Step1Form` (both authenticated and public renders):

```jsx
<Step1Form
    data={data}
    setData={setData}
    errors={errors}
    countries={countries}
    currencies={currencies}
    availableYears={availableYears}    // ← ADD
    processing={processing}
/>
```

---

### Step 9: Frontend — `Step1Form.jsx`

**File:** `resources/js/Components/TaxCalculator/Step1Form.jsx`

Add `availableYears` to props and add a Tax Year dropdown:

```jsx
export default function Step1Form({
    data,
    setData,
    errors,
    countries,
    currencies,
    availableYears,     // ← ADD
    processing,
}) {
    // Existing format options...

    // NEW: Format year options
    const yearOptions = (availableYears || []).map((year) => ({
        value: year,
        label: `${year}`,
    }));
```

Add the Year dropdown field **after** the Currency field (inside the `grid` div), making it a 3-column grid:

```jsx
{/* Replace: grid-cols-1 md:grid-cols-2 → grid-cols-1 md:grid-cols-3 */}
<div className="grid grid-cols-1 md:grid-cols-3 gap-8">
    {/* Annual Gross Income — existing */}
    <div>
        <label ...>Annual Gross Income</label>
        <input ... />
        <InputError ... />
    </div>

    {/* Currency — existing */}
    <div>
        <Select
            label="Currency"
            value={data.currency}
            onChange={(value) => setData("currency", value)}
            options={currencyOptions}
            error={errors.currency}
            placeholder="Select currency"
        />
    </div>

    {/* Tax Year — NEW */}
    <div>
        <Select
            label="Tax Year"
            value={data.tax_year}
            onChange={(value) => setData("tax_year", Number(value))}
            options={yearOptions}
            error={errors.tax_year}
            placeholder="Select year"
        />
    </div>
</div>
```

---

### Step 10: Frontend — `Step2.jsx`

**File:** `resources/js/Pages/TaxCalculator/Step2.jsx`

No structural changes needed. The `step1Data` prop already gets spread into form data (`...step1Data`), which now includes `tax_year`. The `Step2Form` receives `data` which now contains `data.tax_year`.

However, pass `tax_year` explicitly if Step2Form needs it for display:

```jsx
<Step2Form
    data={data}
    setData={setData}
    errors={errors}
    processing={processing}
    onSubmit={handleSubmit}
    onBack={handleBack}
    countries={countries}
    taxTypes={taxTypes}
    taxYear={step1Data.tax_year}     // ← ADD for display
/>
```

---

### Step 11: Frontend — `Step2Form.jsx`

**File:** `resources/js/Components/TaxCalculator/Step2Form.jsx`

Accept `taxYear` prop and replace hardcoded "2024":

```jsx
// Add to props:
export default function Step2Form({
    data, setData, errors, processing,
    onSubmit, onBack, countries, taxTypes,
    taxYear,      // ← ADD
}) {
```

Replace all 3 hardcoded year references:

```jsx
// Line 175-176 — BEFORE:
<h3 className="text-xl font-bold text-primary mb-2">
    Fiscal Year 2024
</h3>

// AFTER:
<h3 className="text-xl font-bold text-primary mb-2">
    Fiscal Year {taxYear || 2026}
</h3>
```

```jsx
// Line 208-210 — BEFORE:
<p className="text-sm text-primary">
    You have <strong>{daysRemaining} days</strong>{" "}
    remaining to account for in 2024.
</p>

// AFTER:
<p className="text-sm text-primary">
    You have <strong>{daysRemaining} days</strong>{" "}
    remaining to account for in {taxYear || 2026}.
</p>
```

---

### Step 12: Frontend — `Form1Summary.jsx`

**File:** `resources/js/Components/TaxCalculator/Form1Summary.jsx`

Add a 4th card showing the selected tax year:

```jsx
{/* Change grid from md:grid-cols-3 to md:grid-cols-4 */}
<div className="grid grid-cols-1 md:grid-cols-4 gap-6">
    {/* ...existing 3 cards... */}

    {/* Tax Year — NEW (4th card) */}
    <div className="flex items-start gap-4">
        <div className="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center">
            <span className="text-primary font-bold text-sm">FY</span>
        </div>
        <div>
            <p className="text-sm text-gray font-medium mb-1">Tax Year</p>
            <p className="text-lg font-bold text-primary">
                {formData.tax_year || 2026}
            </p>
        </div>
    </div>
</div>
```

---

### Step 13: Frontend — `Step3.jsx`

**File:** `resources/js/Pages/TaxCalculator/Step3.jsx`

Show the year in the results heading. Extract `tax_year` from `result`:

```jsx
const {
    annual_income,
    currency,
    tax_year,              // ← ADD
    total_tax,
    // ...rest stays the same
} = result;
```

Update the heading (both authenticated and public renders):

```jsx
// BEFORE:
<h1 className="text-4xl md:text-5xl font-bold text-primary mb-2">
    Tax Calculation Results
</h1>

// AFTER:
<h1 className="text-4xl md:text-5xl font-bold text-primary mb-2">
    Tax Calculation Results — {tax_year || 2026}
</h1>
```

Pass `taxYear` to `DetailedTaxBreakdown`:

```jsx
<DetailedTaxBreakdown
    breakdownData={breakdownData}
    currency={result.currency}
    taxYear={tax_year}           // ← ADD
/>
```

---

### Step 14: Frontend — `DetailedTaxBreakdown.jsx`

**File:** `resources/js/Components/TaxCalculator/DetailedTaxBreakdown.jsx`

#### 14a. Accept `taxYear` prop

```jsx
export default function DetailedTaxBreakdown({ breakdownData, currency, taxYear }) {
```

#### 14b. Add "Year" column header

```jsx
<thead>
    <tr className="border-b-2 border-border-gray">
        <th className="...">Country</th>
        <th className="...">Year</th>           {/* ← ADD */}
        <th className="...">Income Source</th>
        <th className="...">Taxable Amt.</th>
        <th className="...">Tax Rate</th>
        <th className="...">Liability</th>
    </tr>
</thead>
```

#### 14c. Add "Year" cell in each row

```jsx
<td className="py-4 px-4">
    <div className="flex items-center gap-3">
        <span className="font-medium text-primary">
            {item.country_name}
        </span>
    </div>
</td>
{/* ← ADD Year column */}
<td className="py-4 px-4 text-center text-primary font-medium">
    {taxYear || 2026}
</td>
<td className="py-4 px-4 text-gray text-sm">
    Global Income
</td>
```

#### 14d. Update footer colSpan

```jsx
{/* BEFORE: colSpan="4" → AFTER: colSpan="5" */}
<td colSpan="5" className="py-4 px-4 font-bold text-primary text-right">
    Total Tax Liability
</td>
```

#### 14e. Update CSV export to include Year column

```jsx
const handleDownloadCSV = () => {
    const headers = [
        "Country",
        "Year",                // ← ADD
        "Income Source",
        "Taxable Amount",
        "Tax Rate",
        "Liability",
    ];
    const rows = breakdownData.map((item) => [
        item.country_name,
        taxYear || 2026,       // ← ADD
        "Global Income",
        formatCurrency(item.taxable_income),
        `${item.effective_rate}%`,
        formatCurrency(item.tax_due),
    ]);

    // ...rest stays the same
};
```

---

## Data Flow Summary

```
┌──────────────────────────────────────────────────────────────────────┐
│ Step 1: User selects year in dropdown                                │
│   Frontend: tax_year = 2025                                          │
│   POST /step-1 → { annual_income, currency, citizenship_id, tax_year}│
├──────────────────────────────────────────────────────────────────────┤
│ Controller: Validates tax_year via StoreStep1Request                 │
│ Service: saveStep1Data() → saves to user_calculations.tax_year       │
├──────────────────────────────────────────────────────────────────────┤
│ Step 2: Frontend receives tax_year via step1Data                     │
│   Displays "Fiscal Year 2025" dynamically                            │
│   Form1Summary shows "Tax Year: 2025"                                │
├──────────────────────────────────────────────────────────────────────┤
│ Step 3: calculateTaxes() reads $calculation->tax_year                │
│   → TaxCalculationService::calculateBrackets(... $taxYear = 2025)    │
│     → WHERE tax_year = 2025 (queries 2025 brackets)                  │
│   → TreatyResolutionService::applyTreaty(... $taxYear = 2025)        │
│     → WHERE applicable_tax_year = 2025                               │
│   → Result includes tax_year = 2025                                  │
├──────────────────────────────────────────────────────────────────────┤
│ Step 3 UI: "Tax Calculation Results — 2025"                          │
│   DetailedTaxBreakdown table: Year column shows "2025"               │
│   CSV download includes Year column                                  │
└──────────────────────────────────────────────────────────────────────┘
```

## Execution Order

1. Create migration → run `php artisan migrate`
2. Update Model (`UserCalculation.php`)
3. Update Form Request (`StoreStep1Request.php`)
4. Update Controller (`TaxCalculatorController.php`)
5. Update Services (TaxCalculatorService → TaxCalculationService → TreatyResolutionService)
6. Update Frontend: Index.jsx → Step1Form.jsx → Step2.jsx → Step2Form.jsx → Form1Summary.jsx → Step3.jsx → DetailedTaxBreakdown.jsx
7. Test full flow end-to-end
