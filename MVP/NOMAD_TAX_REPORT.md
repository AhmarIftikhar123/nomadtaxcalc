# Nomad Tax Calculator: MVP Analysis & Roadmap

## 🎯 What Problem Is This Actually Solving?
The "Nomad Tax Calculator" solves the **fragmentation and complexity** of tax planning for location-independent individuals.

### The Problem
Digital nomads face a unique set of challenges that standard tax calculators (TurboTax, etc.) cannot handle:
1.  **Multi-Jurisdictional Liability:** They trigger tax obligations in multiple countries within a single year.
2.  **Residency Ambiguity:** Tax residency is often determined by "days spent," which varies wildly (183 days in most, 90 in some, 0 in US).
3.  **Double Taxation:** Paying tax in a host country *and* a home country (especially for US citizens).
4.  **Opaque Rules:** Optimizations like **FEIE** (Foreign Earned Income Exclusion) or **NHR** (Non-Habitual Resident) are powerful but hard to calculate manually.

### The Solution (Your MVP)
Your application aggregates these disparate rules into a single, cohesive workflow:
1.  **Residency Automation:** It automatically determines *where* you owe tax based on your travel calendar.
2.  **Global Aggregation:** It calculates liability for *each* jurisdiction using local progressive brackets.
3.  **Optimization Engine:** It automatically applies complex relief mechanisms like **FEIE** and **Treaty Credits** to show the *Net Effective Rate*.
4.  **Visualization:** It breaks down the "black box" of international tax into a clear, traceable flow.

---

## 🛠 MVP Status: What Works Well
Your current system successfully models the core "Happy Path" for nomads:

-   ✅ **Residency Triggers:** Correctly identifies tax residency based on day count thresholds.
-   ✅ **Income Allocation:** Pro-rates global income to resident countries.
-   ✅ **Progressive Taxation:** Accurately applies tiered tax brackets per country.
-   ✅ **US Citizen Logic:** Correctly separates citizenship taxation (US) from residency taxation, applying FEIE rules.
-   ✅ **Visualization:** The new `TaxCalculationFlow` component makes the logic transparent.

---

## 🚧 Critical Gaps: What Needs to Be Fixed?
To move from "MVP" to a "Production-Grade" tool, you must address these simplifications:

### 1. Treaty Logic is "Heuristic"
**Current:** `TreatyResolutionService` assumes a flat 15% reduction for "credit" treaties (`$tax * 0.85`).
**Actual:** Foreign Tax Credits (FTC) are a exact dollar-for-dollar offset. It should be: `HomeTax = max(0, HomeTax - ForeignTaxPaid)`.
**Fix:** Implement real FTC logic: calculate foreign tax first, then subtract that *exact amount* from home country liability.

### 2. Missing "Social Security" Logic
**Current:** Handled only via manual "Custom Tax" entries.
**Actual:** For many nomads, Social Security (10-20%) is higher than Income Tax. Totalization Agreements prevent paying twice, but they differ from Income Tax treaties.
**Fix:** Add a dedicated `SocialSecurityService` that checks Totalization Agreements separately.

### 3. Fiscal Year Mismatches
**Current:** Assumes a single calendar year (2026).
**Actual:**
-   UK: April 6 – April 5
-   Australia: July 1 – June 30
-   US: Jan 1 – Dec 31
**Fix:** Allow "Split Year" calculations or align inputs to specific fiscal periods.

### 4. Remittance & Territorial Systems
**Current:** Allocates global income to *all* resident countries.
**Actual:**
-   **Thailand/Malta:** Tax only income *remitted* to the country.
-   **Territorial (Georgia/Paraguay):** Tax only *local* income.
**Fix:** Add a `tax_basis` field to Countries (`worldwide`, `territorial`, `remittance`).

### 5. State/Provincial Taxes
**Current:** No US State tax logic.
**Actual:** A US nomad from California owes CA tax even if they live abroad, unless they break domicile.
**Fix:** Add US State selection in Step 1 and apply state-level rules.

---

## 📋 Implementation Plan (Roadmap)

### Phase 1: Logic Hardening (Immediate)
-   [ ] **Refactor Treaty Service:** Replace `0.85` simplified factor with `min(foreign_tax, home_tax)` credit logic.
-   [ ] **Add "Tax Basis":** Allow countries to be marked as 'territorial' so they don't tax global income automatically.

### Phase 2: Feature Expansion
-   [ ] **Social Security Module:** Check Totalization Agreements.
-   [ ] **US State Tax:** Add dropdown for US State (e.g., California vs Texas) to show true liability.

### Phase 3: UX Polish
-   [ ] **Scenario Saver:** Allow users to save multiple travel plans and compare them side-by-side.

User Input
 ├─ Annual Income, Currency, Citizenship
 ├─ Travel / Residency Periods (Country, Days)
 └─ Custom Taxes (optional)
        │
        ▼
+------------------------------+
| Step 1: Residency Determination |
+------------------------------+
        │
        │ For each country:
        │ - Load country rules
        │ - Adjust days (arrival/departure)
        │ - Compare to tax_residency_days threshold
        │ → Tax Resident? Yes/No
        ▼
+------------------------------+
| Step 2: Income Allocation      |
+------------------------------+
        │
        │ Only resident countries enter
        │ Allocation (days-based for Nomads):
        │   allocated_income = annual_income × (days_spent / 365)
        │ Non-residents skipped (unless source-of-income rules)
        ▼
+------------------------------+
| Step 3: Tax Calculation         |
+------------------------------+
        │
        │ For each resident country:
        │ - Check tax system: progressive / flat / remittance / territorial
        │ - Loop through brackets (if progressive):
        │     taxable_in_bracket = min(income, bracket_max) - bracket_min
        │     tax_in_bracket = taxable_in_bracket × rate
        │ - Add custom taxes (Social Security, local levies)
        │ - Sum total tax_due
        │ - Compute country effective_rate = tax_due / allocated_income × 100%
        ▼
+------------------------------+
| Step 4: Treaty / Credit Logic   |
+------------------------------+
        │
        │ Check for active treaty between:
        │  citizenship_country ↔ resident_country
        │ Apply method:
        │  - Credit: home_tax = max(0, home_tax - foreign_tax_paid)
        │  - Exemption: exclude foreign tax from home tax
        ▼
+------------------------------+
| Step 5: US FEIE Calculation     |
+------------------------------+
        │
        │ If citizenship = US:
        │  - Check Physical Presence Test (days outside US ≥ min_days)
        │  - Apply FEIE limit (cap excluded income)
        │  - Reduce US taxable income accordingly
        ▼
+------------------------------+
| Step 6: Aggregation             |
+------------------------------+
        │
        │ - Total Tax = SUM(tax_due for all countries)
        │ - Net Income = annual_income − total_tax
        │ - Effective Rate = total_tax / annual_income × 100%
        ▼
+------------------------------+
| Step 7: Warnings & Recommendations |
+------------------------------+
        │
        │ - Barely resident warnings (threshold ±14 days)
        │ - High-tax vs low-tax recommendations
        │ - NHR / zero-tax country tips
        ▼
+------------------------------+
| Step 8: Persist & Return Results |
+------------------------------+
        │
        │ - Save UserCalculation & UserCalculationCountry records
        │ - Return breakdown_by_country, total_tax, net_income, effective_rate
        │ - Frontend renders TaxCalculationFlow component
