# Tax Calculator — Complete Backend Flow Reference

> **Purpose:** Every time you start a new task touching the tax calculator, read this file first.
> It covers every route, controller method, service call, and DB write from the initial page load through Step 3 results — including the Email and Share Link features.

---

## 1. Routes at a Glance

File: [web.php](file:///c:/xampp/htdocs/inertia/routes/web.php)

| Method | URI | Middleware | Controller Method | Named Route |
|--------|-----|-----------|-------------------|-------------|
| `GET` | `/tax-calculator` | throttle:20 | [`index()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L34) | `tax-calculator.index` |
| `POST` | `/tax-calculator/step-1` | throttle:20 | [`storeStep1()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L94) | `tax-calculator.step-1` |
| `POST` | `/tax-calculator/step-2` | throttle:20 | [`storeStep2()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L111) | `tax-calculator.step-2.store` |
| `GET` | `/tax-calculator/shared/{token}` | throttle:20 | [`viewShared()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L228) | `tax-calculator.shared` |
| `POST` | `/tax-calculator/save` | auth, verified | [`saveCalculation()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L150) | `tax-calculator.save` |
| `POST` | `/tax-calculator/email-results` | auth, verified | [`sendEmail()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L176) | `tax-calculator.email-results` |
| `POST` | `/tax-calculator/generate-link` | auth, verified | [`generateLink()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L205) | `tax-calculator.generate-link` |

---

## 2. Key Files Map

| Layer | File |
|-------|------|
| **Routes** | [routes/web.php](file:///c:/xampp/htdocs/inertia/routes/web.php) |
| **Controller** | [TaxCalculatorController.php](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php) |
| **Main Service (orchestrator)** | [TaxCalculatorService.php](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/TaxCalculatorService.php) |
| **Residency Detection** | [ResidencyDeterminationService.php](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/ResidencyDeterminationService.php) |
| **Tax Maths** | [TaxCalculationService.php](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/TaxCalculationService.php) |
| **Treaty Resolution** | [TreatyResolutionService.php](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/TreatyResolutionService.php) |
| **FEIE (US citizens)** | [FeieCalculationService.php](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/FeieCalculationService.php) |
| **Recommendations** | [RecommendationService.php](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/RecommendationService.php) |
| **Currency Conversion** | [CurrencyService.php](file:///c:/xampp/htdocs/inertia/app/Services/CurrencyService.php) |
| **Step 1 Validation** | [StoreStep1Request.php](file:///c:/xampp/htdocs/inertia/app/Http/Requests/TaxCalculator/StoreStep1Request.php) |
| **Step 2 Validation** | [StoreStep2Request.php](file:///c:/xampp/htdocs/inertia/app/Http/Requests/TaxCalculator/StoreStep2Request.php) |
| **Main Model** | [UserCalculation.php](file:///c:/xampp/htdocs/inertia/app/Models/UserCalculation.php) |
| **Country Pivot Model** | [UserCalculationCountry.php](file:///c:/xampp/htdocs/inertia/app/Models/UserCalculationCountry.php) |
| **Mailable** | [TaxResultsMail.php](file:///c:/xampp/htdocs/inertia/app/Mail/TaxResultsMail.php) |
| **Email Template** | [resources/views/emails/tax-results.blade.php](file:///c:/xampp/htdocs/inertia/resources/views/emails/tax-results.blade.php) |
| **Inertia Middleware** | [HandleInertiaRequests.php](file:///c:/xampp/htdocs/inertia/app/Http/Middleware/HandleInertiaRequests.php) |
| **Frontend — Calculator** | [Pages/TaxCalculator/Index.jsx](file:///c:/xampp/htdocs/inertia/resources/js/Pages/TaxCalculator/Index.jsx) |
| **Frontend — Shared View** | [Pages/SharedCalculation/Show.jsx](file:///c:/xampp/htdocs/inertia/resources/js/Pages/SharedCalculation/Show.jsx) |
| **Frontend — Share Modal** | [Components/Ui/ShareLinkModal.jsx](file:///c:/xampp/htdocs/inertia/resources/js/Components/Ui/ShareLinkModal.jsx) |
| **Migration** | [2026_01_17_000007_create_user_calculations_table.php](file:///c:/xampp/htdocs/inertia/database/migrations/2026_01_17_000007_create_user_calculations_table.php) |

---

## 3. Step-by-Step Flow

### 🔵 PAGE LOAD — `GET /tax-calculator`

**Controller:** [`index()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L34)

```
Browser GETs /tax-calculator
  └─ index() checks for ?calculation_id= query param
      ├─ [EDIT MODE] auth user + ?calculation_id=X
      │   └─ UserCalculation::where(id, user_id)->with('countriesVisited.country')
      │       └─ TaxCalculatorService::rebuildPrefillFromCalculation()
      │           └─ Inertia::render('TaxCalculator/Index') with prefilled data
      └─ [NORMAL MODE] No query param
          └─ Reads session('tax_calc_step1'), session('tax_calc_step2'), session('tax_calc_result')
              └─ Inertia::render('TaxCalculator/Index') with session data (or nulls)
```

**Props passed to frontend:**
- `countries` — all active countries (id, name, iso_code, tax_basis, currency)
- `states` — US states (for domicile_state_id)
- `currencies` — distinct currency list
- `availableYears` — from active `tax_brackets`
- `taxTypes` — system defaults
- `savedStep1Data` — previous Step 1 data (session or DB)
- `savedResidencyPeriods` — previous periods (session or DB)
- `calculationResult` — cached result if coming back
- `editingCalculationId` — non-null only in edit mode

---

### 🟡 STEP 1 — `POST /tax-calculator/step-1`

**Validation:** [`StoreStep1Request`](file:///c:/xampp/htdocs/inertia/app/Http/Requests/TaxCalculator/StoreStep1Request.php)
```
annual_income   required|numeric|min:0
currency        required|string|size:3
citizenship_country_id  required|exists:countries,id
tax_year        required|integer|min:2020|max:2099
domicile_state_id       nullable|exists:states,id
```

**Controller:** [`storeStep1()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L94)

```
POST /tax-calculator/step-1
  └─ TaxCalculatorService::buildSessionStep1Payload()
      └─ Country::findOrFail(citizenship_country_id)  ← resolves country code & name
          └─ session(['tax_calc_step1' => payload])    ← NO DB write
              └─ session()->forget(['tax_calc_step2', 'tax_calc_result'])  ← clears stale data
                  └─ redirect()->route('tax-calculator.index')  ← frontend re-renders at Step 2
```

**Session payload stored (`tax_calc_step1`):**
```json
{
  "citizenship_country_id": 1,
  "citizenship_country_code": "US",
  "citizenship_country_name": "United States",
  "annual_income": 140000,
  "currency": "USD",
  "tax_year": 2026,
  "domicile_state_id": null
}
```

> ⚠️ **No DB write in Step 1.** Pure session storage.

---

### 🟠 STEP 2 — `POST /tax-calculator/step-2`

**Validation:** [`StoreStep2Request`](file:///c:/xampp/htdocs/inertia/app/Http/Requests/TaxCalculator/StoreStep2Request.php) — validates `residency_periods[]`

**Controller:** [`storeStep2()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L111)

```
POST /tax-calculator/step-2
  └─ reads session('tax_calc_step1')  ← if missing: redirect to Step 1 with error
      └─ session(['tax_calc_step2' => $periods])
          └─ Cache::remember(md5(step1 + periods), 1 hour)
              └─ TaxCalculatorService::calculateTaxesFromSession()  ← THE CORE ENGINE
                  └─ session(['tax_calc_result' => $result])
                      └─ back()->with(['calculationResult' => $result])  ← Inertia receives result
```

> ⚠️ **No DB write in Step 2.** Result cached in Laravel Cache (1 hour TTL) keyed by md5 hash of inputs.

---

### ⚙️ CORE CALCULATION ENGINE — `TaxCalculatorService::calculateTaxesFromSession()`

File: [TaxCalculatorService.php](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/TaxCalculatorService.php#L92)

The pipeline runs **6 internal steps** in sequence:

#### Sub-step 1 — Residency Determination
[`ResidencyDeterminationService::determine($periods)`](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/ResidencyDeterminationService.php)
- Compares days_spent against each country's `tax_residency_days` threshold
- Optionally subtracts arrival/departure days per country rule
- Tags each period as `is_tax_resident: true/false`
- Generates near-threshold or barely-resident warnings

#### Sub-step 2 — Tax Per Resident Country
[`TaxCalculationService::allocateIncome()`](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/TaxCalculationService.php) → [`calculateForCountry()`](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/TaxCalculationService.php)
- Only tax-resident periods are taxed
- **Territorial countries** use `local_income` directly (converted to base currency via `CurrencyService`)
- **Worldwide countries** allocate `annual_income × (days/365)`
- `calculateForCountry()` applies progressive brackets, flat tax, or custom tax types from `tax_brackets` table
- State-level taxes applied if `state_id` provided

#### Sub-step 3 — Treaty Resolution
[`TreatyResolutionService::applyTreaty()`](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/TreatyResolutionService.php)
- Looks up treaties between citizenship country and each resident country
- **Credit treaties**: reduce home-country tax by the amount paid abroad
- **Exemption treaties**: eliminate double taxation for non-residents
- **FTC fallback**: even without a formal treaty, applies Foreign Tax Credit if not resident in home country
- Returns updated `$countryBreakdown` + `$treatiesApplied[]`

#### Sub-step 4 — FEIE (US Citizens Only)
[`FeieCalculationService::calculate()`](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/FeieCalculationService.php)
- Only runs if citizenship country is USA (`iso_code = 'US'`)
- Checks `bona_fide_residence` or `physical_presence` test eligibility
- Reads FEIE limit from `settings` table for the given `tax_year`
- If eligible: reduces taxable income for US, recalculates US tax

#### Sub-step 5 — Aggregation
```
total_tax        = sum(tax_due across all countries)
net_income       = annual_income - total_tax
effective_rate   = (total_tax / annual_income) × 100
```

#### Sub-step 6 — Recommendations & Warnings
[`RecommendationService::generate()`](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/RecommendationService.php)
- Generates actionable tax optimization tips
- [`ResidencyDeterminationService::generateWarnings()`](file:///c:/xampp/htdocs/inertia/app/Services/TaxCalculator/ResidencyDeterminationService.php) — near-threshold alerts

**Final result shape:**
```json
{
  "annual_income": 140000,
  "currency": "USD",
  "tax_year": 2026,
  "total_tax": 44175,
  "net_income": 95825,
  "effective_tax_rate": 31.57,
  "breakdown_by_country": [...],
  "residency_warnings": [...],
  "residency_data": [...],
  "comparison_data": [...],
  "treaties_applied": [...],
  "feie_result": {...},
  "recommendations": [...]
}
```

---

### 🟢 STEP 3 — Results Page (Frontend-Driven)

The frontend receives the result from `calculationResult` prop (or `flash.calculationResult` after Step 2 submit).

Frontend file: [Index.jsx](file:///c:/xampp/htdocs/inertia/resources/js/Pages/TaxCalculator/Index.jsx)

**Result display components (all in** [Components/TaxCalculator/](file:///c:/xampp/htdocs/inertia/resources/js/Components/TaxCalculator)**)**:

| Component | Data Used |
|-----------|-----------|
| `ResultMetricsCards` | total_tax, net_income, effective_tax_rate |
| `TaxCalculationFlow` | breakdown_by_country |
| `TaxLiabilityComparison` | comparison_data |
| `DetailedTaxBreakdown` | breakdown_by_country |
| `TreatiesApplied` | treaties_applied (keys: `countries[]`, `type`, `tax_saved`) |
| `FEIEStatus` | feie_result |
| `ResidencyInsights` | residency_data |
| `SmartRecommendations` | recommendations |
| `ResidencyRiskAlert` | residency_warnings |

---

### 💾 SAVE CALCULATION — `POST /tax-calculator/save` *(auth + verified)*

**Controller:** [`saveCalculation()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L150)

```
POST /tax-calculator/save  { calculation_id: null | existingId }
  └─ reads session(tax_calc_step1, step2, result)
      └─ TaxCalculatorService::saveCalculationForUser()
          ├─ [CREATE] calculation_id is null  → UserCalculation::create()
          └─ [UPDATE] calculation_id present  → UserCalculation::where(id, user_id)->update()
              └─ countriesVisited()->delete() then re-create UserCalculationCountry rows
                  └─ back()->with('saved_calculation_id', $id)   ← triggers flash useEffect in frontend
```

**DB Tables Written:**
- [`user_calculations`](file:///c:/xampp/htdocs/inertia/database/migrations/2026_01_17_000007_create_user_calculations_table.php) — main record
- `user_calculation_countries` — one row per residency period

**Flash key forwarded by middleware:** `saved_calculation_id` → `flash.saved_calculation_id` in React

---

### 📧 EMAIL RESULTS — `POST /tax-calculator/email-results` *(auth + verified)*

**Controller:** [`sendEmail()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L176)

```
POST /tax-calculator/email-results  { calculation_id: X }
  └─ UserCalculation::where(id, user_id)->firstOrFail()
      └─ if !isShareActive():  generate share_token + share_expires_at = +30 days
          └─ $shareUrl = route('tax-calculator.shared', share_token)
              └─ Mail::to(user->email)->send(new TaxResultsMail($calculation, $shareUrl))
                  └─ $calculation->update(['email_sent_at' => now()])
                      └─ back()->with('success', 'Results sent to ...')
```

**Mailable:** [TaxResultsMail.php](file:///c:/xampp/htdocs/inertia/app/Mail/TaxResultsMail.php)
**Template:** [emails/tax-results.blade.php](file:///c:/xampp/htdocs/inertia/resources/views/emails/tax-results.blade.php)

> **Dev tip:** Set `MAIL_MAILER=log` → emails captured in `storage/logs/laravel.log`

---

### 🔗 GENERATE SHARE LINK — `POST /tax-calculator/generate-link` *(auth + verified)*

**Controller:** [`generateLink()`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L205)

```
POST /tax-calculator/generate-link  { calculation_id: X }
  └─ UserCalculation::where(id, user_id)->firstOrFail()
      └─ update({ share_token: Str::random(64), share_expires_at: now()+30d })
          └─ back()->with('share_url', route('tax-calculator.shared', token))
```

**Inertia middleware** ([HandleInertiaRequests.php](file:///c:/xampp/htdocs/inertia/app/Http/Middleware/HandleInertiaRequests.php#L44)) forwards `share_url` → `flash.share_url`

**Frontend:** `useEffect(() => { if (flash?.share_url) setShowShareModal(true) }, [flash?.share_url])`
→ Opens [ShareLinkModal.jsx](file:///c:/xampp/htdocs/inertia/resources/js/Components/Ui/ShareLinkModal.jsx)

---

### 🌐 VIEW SHARED PAGE — `GET /tax-calculator/shared/{token}` *(public)*

**Controller:** [`viewShared(string $token)`](file:///c:/xampp/htdocs/inertia/app/Http/Controllers/TaxCalculator/TaxCalculatorController.php#L228)

```
GET /tax-calculator/shared/{token}
  └─ UserCalculation::where('share_token', $token)->with('countriesVisited.country')
      ├─ not found → Inertia::render('SharedCalculation/Show', { expired: true })
      ├─ isShareActive() === false → Inertia::render with { expired: true, expiredAt: date }
      └─ valid → builds result array from stored JSON columns
          └─ Inertia::render('SharedCalculation/Show', { expired: false, result, shareExpiresAt })
```

**Frontend:** [SharedCalculation/Show.jsx](file:///c:/xampp/htdocs/inertia/resources/js/Pages/SharedCalculation/Show.jsx)

---

## 4. Session Keys Reference

| Key | Set By | Cleared By | Contains |
|-----|--------|------------|---------|
| `tax_calc_step1` | `storeStep1()` | `storeStep1()` on re-submit | Income, currency, citizenship, year |
| `tax_calc_step2` | `storeStep2()` | `storeStep1()` on re-submit | Residency periods array |
| `tax_calc_result` | `storeStep2()` | `storeStep1()` on re-submit | Full result JSON |

---

## 5. DB Columns — `user_calculations`

Migration: [2026_01_17_000007_create_user_calculations_table.php](file:///c:/xampp/htdocs/inertia/database/migrations/2026_01_17_000007_create_user_calculations_table.php)

| Column | Type | Notes |
|--------|------|-------|
| `share_token` | `string(64)\|unique\|null` | Random 64-char token for public share URLs |
| `share_expires_at` | `timestamp\|null` | Set to now()+30 days when link/email generated |
| `email_sent_at` | `timestamp\|null` | Updated when email successfully dispatched |
| `tax_breakdown` | `json` | Stored `breakdown_by_country` array |
| `treaty_applied` | `json` | Stores `[{countries[], type, tax_saved}]` |
| `feie_result` | `json` | Stores FEIE calculation result |

---

## 6. Inertia Flash Keys

All flash data must be whitelisted in [HandleInertiaRequests.php](file:///c:/xampp/htdocs/inertia/app/Http/Middleware/HandleInertiaRequests.php#L44):

| Flash Key | Set By | Purpose |
|-----------|--------|---------|
| `success` | Multiple controllers | Toast / success message |
| `error` | Multiple controllers | Toast / error message |
| `saved_calculation_id` | `saveCalculation()` | Frontend sets `savedCalculationId` state |
| `share_url` | `generateLink()` | Frontend opens `ShareLinkModal` |

> ⚠️ **Adding a new flash key?** You MUST add it to `HandleInertiaRequests::share()` or it will never reach React.

---

## 7. Data Flow Summary

```
Browser
  │
  ├─ Step 1 form submit → POST /step-1
  │     └─ session[tax_calc_step1] = {income, currency, country, year}
  │
  ├─ Step 2 form submit → POST /step-2
  │     └─ session[tax_calc_step2] = [{country_id, days_spent, ...}]
  │     └─ Cache::remember → TaxCalculatorService::calculateTaxesFromSession()
  │           ├─ ResidencyDeterminationService
  │           ├─ TaxCalculationService (per country)
  │           ├─ TreatyResolutionService
  │           ├─ FeieCalculationService (US only)
  │           ├─ RecommendationService
  │           └─ returns result{}
  │     └─ session[tax_calc_result] = result{}
  │     └─ back()->with(calculationResult)  ← Inertia renders Step 3
  │
  ├─ "Save Calculation" → POST /save  [auth]
  │     └─ TaxCalculatorService::saveCalculationForUser()
  │           → user_calculations + user_calculation_countries (DB write)
  │     └─ flash[saved_calculation_id]  → frontend unlocks Email/Share buttons
  │
  ├─ "Email Results" → POST /email-results  [auth]
  │     └─ auto-generate share_token if needed
  │     └─ Mail::send(TaxResultsMail)
  │     └─ user_calculations.email_sent_at = now()
  │
  ├─ "Share Link" → POST /generate-link  [auth]
  │     └─ refreshes share_token + share_expires_at
  │     └─ flash[share_url] → frontend opens ShareLinkModal
  │
  └─ Public share URL → GET /shared/{token}
        └─ validates token + expiry
        └─ Inertia::render('SharedCalculation/Show')
```
