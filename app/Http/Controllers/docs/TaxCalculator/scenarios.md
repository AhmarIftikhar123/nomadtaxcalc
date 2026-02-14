# Tax Calculator - Detailed Scenarios

This document provides detailed walk-throughs of various tax calculation scenarios with expected outcomes.

---

## Scenario Index

1. [Zero-Tax Digital Nomad (UAE)](#scenario-1-zero-tax-digital-nomad-uae)
2. [FEIE-Eligible US Citizen](#scenario-2-feie-eligible-us-citizen)
3. [Multi-Country with No Residency](#scenario-3-multi-country-with-no-residency)
4. [Treaty Application (USA-UK)](#scenario-4-treaty-application-usa-uk)
5. [Barely Resident Warning](#scenario-5-barely-resident-warning)
6. [Near Threshold Alert](#scenario-6-near-threshold-alert)
7. [Non-FEIE US Citizen](#scenario-7-non-feie-us-citizen)
8. [Tax Optimization Recommendation](#scenario-8-tax-optimization-recommendation)
9. [Multiple Residencies](#scenario-9-multiple-residencies)
10. [Progressive vs Flat Tax](#scenario-10-progressive-vs-flat-tax)

---

## Scenario 1: Zero-Tax Digital Nomad (UAE)

### Input
```json
{
  "citizenship": "UK",
  "annual_income": 100000,
  "currency": "USD",
  "residency_periods": [
    {"country": "UAE", "days": 365}
  ]
}
```

### Processing Flow

**Step 2: Residency Determination**
```
UAE:
  threshold = 183 days
  days_spent = 365
  is_tax_resident = true (365 >= 183)
```

**Step 3: Tax Calculation**
```
UAE allocated income: (100000 / 365) * 365 = $100,000
UAE tax rate: 0% (flat)
UAE tax: $0
```

**Step 3: Treaties**
```
No double taxation (only one country)
```

**Step 3: FEIE**
```
Not applicable (not US citizen)
```

### Expected Result
```json
{
  "total_tax": 0,
  "net_income": 100000,
  "effective_tax_rate": 0,
  "breakdown_by_country": [
    {
      "country_name": "Dubai / UAE",
      "days_spent": 365,
      "allocated_income": 100000,
      "taxable_income": 100000,
      "tax_due": 0,
      "effective_rate": 0,
      "is_tax_resident": true,
      "method": "flat"
    }
  ],
  "recommendations": [
    {
      "type": "zero_tax",
      "priority": "high",
      "title": "You're in a zero-tax jurisdiction",
      "message": "UAE has no personal income tax. Maximize your stay!"
    }
  ]
}
```

---

## Scenario 2: FEIE-Eligible US Citizen

### Input
```json
{
  "citizenship": "USA",
  "annual_income": 120000,
  "currency": "USD",
  "residency_periods": [
    {"country": "Thailand", "days": 350}
  ]
}
```

### Processing Flow

**Step 2: Residency Determination**
```
Thailand:
  threshold = 180 days
  days_spent = 350
  is_tax_resident = true (350 >= 180)
```

**Step 3: Tax Calculation**
```
Thailand allocated income: (120000 / 365) * 350 = $115,068
Thailand tax: $0 (doesn't tax foreign-sourced income)

USA worldwide income: $120,000
USA tax (before FEIE): ~$18,201.92 (progressive)
```

**Step 3: FEIE Check**
```
Days outside US: 350
Minimum required: 330
Eligible: YES

FEIE limit (2026): $126,500
Excluded income: min(120000, 126500) = $120,000
Taxable in US: $0
```

**Step 3: Final Calculation**
```
USA tax after FEIE: $0
Total tax: $0
```

### Expected Result
```json
{
  "total_tax": 0,
  "net_income": 120000,
  "effective_tax_rate": 0,
  "feie_result": {
    "eligible": true,
    "days_outside_us": 350,
    "minimum_required": 330,
    "feie_limit": 126500,
    "excluded_income": 120000,
    "taxable_us_income": 0,
    "reason": "Qualified under Physical Presence Test (350 days outside US exceeds 330 days)"
  }
}
```

---

## Scenario 3: Multi-Country with No Residency

### Input
```json
{
  "citizenship": "Canada",
  "annual_income": 80000,
  "currency": "USD",
  "residency_periods": [
    {"country": "Mexico", "days": 120},
    {"country": "Portugal", "days": 100},
    {"country": "Spain", "days": 90},
    {"country": "Thailand", "days": 55}
  ]
}
```

### Processing Flow

**Step 2: Residency Determination**
```
Mexico: 120 < 183 → NOT tax resident
Portugal: 100 < 183 → NOT tax resident
Spain: 90 < 183 → NOT tax resident
Thailand: 55 < 180 → NOT tax resident
```

**Step 3: Tax Calculation**
```
No tax resident countries
Total tax: $0
```

**Step 3: Recommendations**
```
"No tax residency triggered. Continue optimizing your travel patterns."
```

### Expected Result
```json
{
  "total_tax": 0,
  "net_income": 80000,
  "effective_tax_rate": 0,
  "breakdown_by_country": [],
  "recommendations": [
    {
      "type": "perpetual_traveler",
      "priority": "medium",
      "title": "Perpetual traveler status",
      "message": "You avoided tax residency in all countries. Maintain this pattern for continued optimization."
    }
  ]
}
```

---

## Scenario 4: Treaty Application (USA-UK)

### Input
```json
{
  "citizenship": "USA",
  "annual_income": 150000,
  "currency": "USD",
  "residency_periods": [
    {"country": "UK", "days": 200},
    {"country": "USA", "days": 165}
  ]
}
```

### Processing Flow

**Step 2: Residency Determination**
```
UK: 200 >= 183 → Tax resident
USA: 165 < 183 → NOT resident (but taxed on worldwide income)
```

**Step 3: Tax Calculation**
```
UK allocated: (150000 / 365) * 200 = $82,192
UK tax (progressive): ~$16,438

USA worldwide: $150,000
USA tax (progressive): ~$28,012
```

**Step 3: Treaty Application**
```
Treaty found: USA-UK (Credit method)
US tax reduced by 15% credit:
$28,012 * 0.85 = $23,810
```

**Step 3: Final**
```
Total tax: $16,438 (UK) + $23,810 (US) = $40,248
Effective rate: 26.83%
```

### Expected Result
```json
{
  "total_tax": 40248,
  "net_income": 109752,
  "effective_tax_rate": 26.83,
  "treaties_applied": [
    {
      "countries": ["United States", "United Kingdom"],
      "type": "credit",
      "tax_saved": 4202
    }
  ]
}
```

---

## Scenario 5: Barely Resident Warning

### Input
```json
{
  "citizenship": "Germany",
  "annual_income": 90000,
  "currency": "EUR",
  "residency_periods": [
    {"country": "Spain", "days": 185}
  ]
}
```

### Processing Flow

**Step 2: Residency Determination**
```
Spain:
  threshold = 183
  days_spent = 185
  days_diff = 185 - 183 = 2 days
  is_tax_resident = true
```

**Step 3: Warnings**
```
Type: "barely_resident"
Message: "You became a tax resident of Spain by only 2 days. 
         Small adjustments to your travel could change this."
```

### Expected Result
```json
{
  "residency_warnings": [
    {
      "country": "Spain",
      "type": "barely_resident",
      "message": "You became a tax resident of Spain by only 2 days. Small adjustments to your travel could change this."
    }
  ]
}
```

---

## Scenario 6: Near Threshold Alert

### Input
```json
{
  "citizenship": "France",
  "annual_income": 70000,
  "currency": "EUR",
  "residency_periods": [
    {"country": "Portugal", "days": 175}
  ]
}
```

### Processing Flow

**Step 2: Residency Determination**
```
Portugal:
  threshold = 183
  days_spent = 175
  days_diff = 183 - 175 = 8 days remaining
  is_tax_resident = false
```

**Step 3: Warnings**
```
Type: "near_threshold"
Message: "You spent 175 days in Portugal, just 8 days below 
         the tax residency threshold. Consider this for future planning."
```

### Expected Result
```json
{
  "residency_warnings": [
    {
      "country": "Portugal",
      "type": "near_threshold",
      "message": "You spent 175 days in Portugal, just 8 days below the tax residency threshold. Consider this for future planning."
    }
  ]
}
```

---

## Scenario 7: Non-FEIE US Citizen

### Input
```json
{
  "citizenship": "USA",
  "annual_income": 200000,
  "currency": "USD",
  "residency_periods": [
    {"country": "UK", "days": 250},
    {"country": "USA", "days": 115}
  ]
}
```

### Processing Flow

**Step 3: FEIE Check**
```
Days outside US: 250
Minimum required: 330
Eligible: NO
Days needed: 330 - 250 = 80 more days
```

### Expected Result
```json
{
  "feie_result": {
    "eligible": false,
    "days_outside_us": 250,
    "minimum_required": 330,
    "reason": "Not qualified - spent only 250 days outside US, need 80 more days to reach 330"
  }
}
```

---

## Scenario 8: Tax Optimization Recommendation

### Input
```json
{
  "citizenship": "Australia",
  "annual_income": 180000,
  "currency": "AUD",
  "residency_periods": [
    {"country": "UK", "days": 200},
    {"country": "USA", "days": 165}
  ]
}
```

### Processing Flow

**Step 3: Tax Calculation**
```
UK: Tax resident, tax = $36,000
USA: Not resident for Australia, $0
Total: $36,000
```

**Step 3: Recommendations**
```
Find highest tax country: UK with $36,000
Generate recommendation:
"You paid the most tax in United Kingdom (AUD 36,000). 
 Consider reducing your stay below the 183 day threshold."
```

### Expected Result
```json
{
  "recommendations": [
    {
      "type": "tax_optimization",
      "priority": "high",
      "title": "Reduce time in high-tax countries",
      "message": "You paid the most tax in United Kingdom (AUD 36,000). Consider reducing your stay below the 183 day threshold."
    }
  ]
}
```

---

## Scenario 9: Multiple Residencies

### Input
```json
{
  "citizenship": "Ireland",
  "annual_income": 130000,
  "currency": "EUR",
  "residency_periods": [
    {"country": "UK", "days": 190},
    {"country": "Portugal", "days": 175}
  ]
}
```

### Processing Flow

**Step 2: Residency Determination**
```
UK: 190 >= 183 → Tax resident
Portugal: 175 < 183 → NOT tax resident
```

**Step 3: Tax Calculation**
```
UK allocated: (130000 / 365) * 190 = €67,671
UK tax: ~€13,534
```

### Expected Result
```json
{
  "breakdown_by_country": [
    {
      "country_name": "United Kingdom",
      "is_tax_resident": true,
      "tax_due": 13534
    }
  ],
  "residency_warnings": [
    {
      "country": "Portugal",
      "type": "near_threshold",
      "message": "You spent 175 days in Portugal, just 8 days below the tax residency threshold."
    }
  ]
}
```

---

## Scenario 10: Progressive vs Flat Tax

### Input A (Progressive - USA)
```json
{
  "citizenship": "USA",
  "annual_income": 100000,
  "currency": "USD",
  "residency_periods": [
    {"country": "USA", "days": 365}
  ]
}
```

**USA Tax Calculation (2026 Brackets):**
```
$0 - $11,600: $11,600 * 0.10 = $1,160
$11,601 - $47,150: $35,550 * 0.12 = $4,266
$47,151 - $100,000: $52,850 * 0.22 = $11,627
Total: $17,053
Effective rate: 17.05%
```

### Input B (Flat - UAE)
```json
{
  "citizenship": "India",
  "annual_income": 100000,
  "currency": "USD",
  "residency_periods": [
    {"country": "UAE", "days": 365}
  ]
}
```

**UAE Tax Calculation:**
```
$100,000 * 0% = $0
Effective rate: 0%
```

### Comparison
```
Same income ($100,000), different countries:
USA (progressive): $17,053 tax (17.05%)
UAE (flat): $0 tax (0%)
Savings: $17,053
```

---

## Testing Commands

To test these scenarios:

```bash
# Test via Postman/cURL
curl -X POST http://localhost/tax-calculator/step-1 \
  -H "Content-Type: application/json" \
  -d '{"annual_income": 100000, "currency": "USD", "citizenship_country_id": 3}'

# Test via browser
1. Navigate to /tax-calculator
2. Enter scenario inputs
3. Verify results match expected outputs
```

---

**Last Updated:** 2026-01-25  
**Version:** 1.0.0
