# Tax Calculator — Feature Guide

## Overview

The Tax Calculator is a **3-step wizard** that estimates the income tax liability for digital nomads who split their year across multiple countries. It considers:

- **Progressive tax brackets** (sourced from PwC/KPMG/government data for 40 countries)
- **Flat tax rates** for countries like Hungary (15%), Romania (10%), Bulgaria (10%)
- **Tax residency determination** using each country's day threshold, arrival/departure rules
- **Double taxation treaties** (79 bilateral pairs) with credit/exemption methods
- **FEIE** (Foreign Earned Income Exclusion) for US citizens living abroad
- **Smart recommendations** for tax optimization

### How Currency Works

The system does **NOT** perform currency conversion. Tax rates are percentages, which are universal regardless of currency denomination. The user's chosen currency is used for display purposes only. Each country's brackets are applied to the proportionally allocated income in whatever currency the user enters.

> **Example**: A user earning 300,000 SAR who spent 200 days in Saudi Arabia and 165 days in the UK would see: Saudi portion = 300,000 × (200/365), UK portion = 300,000 × (165/365). Tax *rates* apply to these amounts identically whether they're denoted in SAR, USD, or GBP.

---

## The 3-Step Flow

| Step | Page | Purpose |
|------|------|---------|
| 1 | `/tax-calculator` | Enter income, currency, citizenship country |
| 2 | `/tax-calculator/step-2` | Add countries visited + days spent (must = 365) |
| 3 | `/tax-calculator/step-3` | View results: tax breakdown, treaties, FEIE, recommendations |

---

## Case Study 1: US Citizen Digital Nomad

### Profile
- **Income**: $120,000 USD
- **Citizenship**: United States
- **Travel**: 200 days in Portugal, 165 days in United States

### Step 1
User enters $120,000, selects USD, selects "United States".

### Step 2
Adds two countries:
- Portugal: 200 days → **tax resident** (exceeds 183-day threshold)
- United States: 165 days → not tax resident in US (by days), but US taxes citizens worldwide

### Step 3 — Results

**Residency Determination**:
- Portugal: 200 days ≥ 183 → Tax Resident ✅
- US: 165 days < 183 → Below threshold, but US taxes worldwide regardless

**Income Allocation**:
- Portugal: $120,000 × (200/365) = **$65,753.42**
- US: $120,000 × (165/365) = **$54,246.58**

**Tax Calculation**:
- Portugal progressive brackets applied to $65,753.42
- US federal brackets applied to $54,246.58

**FEIE Applied**: User spent 200 days outside US ≥ 330 required? No (only 200). **FEIE not eligible**.

**Treaty Applied**: US-Portugal treaty (credit method) → reduces double taxation by ~15% credit.

**Residency Warning**: "You spent 200 days in Portugal, exceeding the 183-day threshold. This triggers worldwide income liability."

**Recommendation**: "You exceeded the threshold by only 17 days. With minor travel adjustments, you could avoid tax residency next year."

---

## Case Study 2: EU Freelancer Across Multiple Countries

### Profile
- **Income**: €85,000 EUR
- **Citizenship**: Germany
- **Travel**: 100 days Germany, 150 days Spain, 115 days Portugal

### Step 2
- Germany: 100 days → not tax resident (< 183)
- Spain: 150 days → not tax resident (< 183)
- Portugal: 115 days → not tax resident (< 183)

### Step 3 — Results

**Key Insight**: No country surpasses the 183-day threshold. The user is **not a tax resident** in any country visited.

**Income Allocation** (proportional):
- Germany: €85,000 × (100/365) = **€23,287.67**
- Spain: €85,000 × (150/365) = **€34,931.51**
- Portugal: €85,000 × (115/365) = **€26,780.82**

**Tax Calculated**: Even without formal residency, the service applies progressive rates to each allocated portion as a conservative estimate.

**Treaties Applied**:
- Germany-Spain DTA (credit method)
- Germany-Portugal DTA (credit method)

**Residency Warning**: "You spent 150 days in Spain, just 33 days below the tax residency threshold. Consider this for future planning."

**Recommendation**: "Reduce time in high-tax countries. Consider zero-tax jurisdictions like UAE or Georgia."

---

## Case Study 3: UAE-Based Professional (Zero Tax)

### Profile
- **Income**: 350,000 AED
- **Citizenship**: United Arab Emirates
- **Travel**: 300 days UAE, 65 days Thailand

### Step 2
- UAE: 300 days → tax resident (but no income tax!)
- Thailand: 65 days → not tax resident (< 180)

### Step 3 — Results

**Residency Determination**:
- UAE: 300 days → Resident, but **0% income tax**
- Thailand: 65 days → Below threshold

**Tax Calculated**:
- UAE: 350,000 × (300/365) = 287,671 AED → Tax = **0 AED** (no income tax)
- Thailand: 350,000 × (65/365) = 62,329 AED → Below threshold, no residency tax

**Total Tax**: **0 AED**

**ResidencyRiskAlert**: Shows a **green banner** → "No Income Tax in United Arab Emirates. You spent 300 days in UAE, which has no personal income tax. Time spent here does not generate additional tax liability."

**No red/yellow alert** because UAE has no income tax.

**Recommendation**: "You're already in a zero-tax jurisdiction. No optimization needed."

---

## Key Technical Details

| Component | Description |
|-----------|-------------|
| `TaxCalculatorController` | 3-step wizard controller with session-based state |
| `TaxCalculatorService` | Orchestrator — calls 5 sub-services |
| `TaxCalculationService` | Progressive/flat tax bracket engine |
| `ResidencyDeterminationService` | Day counting with arrival/departure rules |
| `TreatyResolutionService` | 79 DTAs — credit, exemption, partial methods |
| `FeieCalculationService` | US-only, Physical Presence Test (330 days) |
| `RecommendationService` | Optimization suggestions at runtime |

# IMPORTANT

Our system does NOT do currency conversion. The user enters their income in whichever currency they choose, and the system treats that as the base amount. Tax brackets are stored in each country's local currency, so the calculation is bracket-native. The currency is only used for display purposes in the results.

Example: A user in Saudi Arabia earning 300,000 SAR selects SAR as their currency. The system calculates Saudi tax using SAR brackets directly. If they also lived 200 days in the UK, the system allocates income proportionally and applies UK GBP brackets to the allocated amount — but displays the result in the user's chosen currency (SAR). This works because the tax rates (percentages) are universal regardless of currency denomination.