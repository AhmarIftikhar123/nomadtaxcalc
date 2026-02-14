# 🚀 NomadTax MVP — Feature List & Todos

> **Goal:** Build an MVP tax calculator for digital nomads that covers multi-country tax calculation, FEIE, country comparison, residency risk, SEO guides, and a blog — all monetised via AdSense & affiliate links.

---

## Phases Overview

| Phase | Timeframe | Focus |
|-------|-----------|-------|
| **Phase 1** | Week 1–4 | Core Calculator |
| **Phase 2** | Week 5–6 | Comparison, SEO & Content |
| **Phase 3** | Week 7–8 | Polish & Launch |

---

## Phase 1 — Core Calculator (Week 1–4)

### Feature 1: Multi-Country Tax Calculator

> **Problem:** "I worked in Portugal for 200 days, Spain for 120 days, and Thailand for 45 days. Where do I owe taxes and how much?"

- [ ] **Step 1/4 — Income & Citizenship**
  - [ ] Input: annual income (number, currency selector)
  - [ ] Input: citizenship country (searchable dropdown)
  - [ ] Client-side validation (required, positive number)
- [ ] **Step 2/4 — Countries Visited**
  - [ ] "Add Country" row: country dropdown + days input
  - [ ] Running total of days (must equal 365)
  - [ ] Real-time 183-day warning badges per country
  - [ ] Allow add / remove country rows
- [ ] **Step 3/4 — Review & Calculate**
  - [ ] Summary card showing income, citizenship, countries
  - [ ] "Calculate" button → POST to backend
- [ ] **Backend Processing**
  - [ ] Fetch tax brackets, residency rules, treaties, FEIE settings from DB
  - [ ] Determine tax residency per country (183-day rule + extras)
  - [ ] Calculate progressive tax using brackets
  - [ ] Apply foreign tax credits / treaties where applicable
  - [ ] Detect US citizen → trigger FEIE sub-flow
  - [ ] Sum total tax liability across all countries
  - [ ] Generate optimisation recommendations
  - [ ] Save calculation to `user_calculations`
  - [ ] Track analytics event (countries, income range)
- [ ] **Step 4/4 — Results Page**
  - [ ] Per-country tax breakdown cards
  - [ ] Total liability + effective rate
  - [ ] Residency warnings (colour-coded)
  - [ ] Recommendation cards
  - [ ] Affiliate links (Wise, ExpressVPN, tax services)
  - [ ] Google Ads slot
  - [ ] "Adjust Inputs" / "Compare Countries" / "Share Results" actions

---

### Feature 2: 183-Day Rule Tracker

> **Problem:** "I'm in Spain. How many more days until I become a tax resident?"

- [ ] Visual progress bar per country (% toward 183)
- [ ] Colour-coded risk levels
  - 🟢 GREEN: < 150 days (safe)
  - 🟡 YELLOW: 150–182 days (approaching)
  - 🔴 RED: ≥ 183 days (tax resident)
- [ ] Days-remaining counter text
- [ ] Instant re-render when user edits days
- [ ] Integrated into Step 2 of calculator

---

### Feature 3: FEIE Calculator (US Citizens)

> **Problem:** "I'm a US citizen abroad. Can I exclude my income from US taxes?"

- [ ] Auto-detect US citizenship from Step 1
- [ ] Fetch FEIE limit from `settings` table (2026 = $130,000)
- [ ] Physical Presence Test: ≥ 330 days outside US?
- [ ] Full vs partial exclusion logic
  - Income ≤ FEIE → full exclusion, US tax = $0
  - Income > FEIE → partial, tax on remainder
- [ ] Results badge: "✅ FEIE Qualified" / "❌ Does Not Qualify"
- [ ] Explanation panel: what is FEIE, Form 2555, Physical Presence Test
- [ ] Link to "Complete FEIE Guide 2026" blog post
- [ ] Affiliate CTA: tax filing services

---

### Feature 4: Results Page with Tax Breakdown

- [ ] Responsive results layout (mobile-first)
- [ ] Per-country cards: country flag, tax amount, effective rate, residency status
- [ ] Total tax summary card
- [ ] Chart: horizontal bar comparing countries
- [ ] Downloadable / shareable URL (query-string encoded)
- [ ] CTA sections: affiliate links, ads, related guides

---

## Phase 2 — Comparison, SEO & Content (Week 5–6)

### Feature 5: Country Comparison Tool

> **Problem:** "Should I live in Portugal or Spain? Which has better taxes?"

- [ ] Comparison modal / page
- [ ] Pre-fill with user's current countries
- [ ] Allow adding extra countries (searchable dropdown)
- [ ] For each country calculate tax on same income assuming residency
- [ ] Horizontal bar chart sorted lowest → highest
- [ ] Metadata per country: DN visa?, min income, cost of living, threshold
- [ ] Highlight best option with 🏆 badge
- [ ] "Change Income" recalculates inline
- [ ] Track user interest per country for analytics

---

### Feature 6: Tax Residency Risk Assessment

> **Problem:** "Am I accidentally becoming a tax resident somewhere?"

- [ ] Auto-run after Step 2 (countries entered)
- [ ] Per-country risk level: HIGH / MEDIUM / LOW
- [ ] Days over/remaining counter
- [ ] Additional rules check (center of vital interests, permanent home)
- [ ] Generate warnings & recommendations
- [ ] Risk Assessment Card on results page (sorted by risk)
- [ ] Colour badges: 🔴 🟡 🟢
- [ ] Action items: reduce days, keep docs, consult advisor

---

### Feature 7: Country Tax Guide Pages (SEO)

> **Problem:** "I need detailed tax info about Portugal for digital nomads."

- [ ] Dynamic route: `/country/{slug}-tax-guide`
- [ ] 20 countries at launch (top DN destinations)
- [ ] Page sections:
  - [ ] Hero with country name + flag
  - [ ] Quick Calculator CTA
  - [ ] Tax Rates Overview (brackets table)
  - [ ] 183-Day Rule explanation
  - [ ] Digital Nomad Visa info (requirements, cost, duration)
  - [ ] Tax Residency Rules
  - [ ] FEIE section (for US citizens)
  - [ ] Cost of Living snapshot
  - [ ] Pros & Cons
  - [ ] Related blog articles
  - [ ] Affiliate links + ads
- [ ] SEO: unique title, meta description, OG tags, JSON-LD
- [ ] Internal linking to calculator & comparison tool
- [ ] Breadcrumbs

---

### Feature 8: Blog System (SEO Content)

- [ ] 15 articles minimum at launch
- [ ] Categories: Tax Guides, FEIE, Country Spotlights, Nomad Tips
- [ ] Article page: hero, body (rich text), author, date, related posts
- [ ] Blog index with pagination & category filter
- [ ] SEO: unique title, meta, OG, JSON-LD Article schema
- [ ] Internal CTAs: calculator, comparison tool, guides
- [ ] Ad slots (sidebar + in-content)

---

## Phase 3 — Polish & Launch (Week 7–8)

### Analytics & Tracking

- [ ] Google Analytics 4 integration
- [ ] Custom events: calculation completed, affiliate click, guide view
- [ ] UTM parameter support
- [ ] Income-range & country-interest heatmap (internal dashboard)

### AdSense Integration

- [ ] Ad slots: results page, guide sidebar, blog in-content, comparison page
- [ ] Responsive ad units
- [ ] Lazy-load ads for performance
- [ ] GDPR cookie consent banner

### Mobile Optimisation

- [ ] Responsive breakpoints for all calculator steps
- [ ] Touch-friendly country selector
- [ ] Bottom-sheet modals on mobile
- [ ] Performance budget: < 3 s LCP

### Legal Disclaimers

- [ ] Global disclaimer banner: "Not tax advice"
- [ ] Per-result disclaimer
- [ ] Privacy Policy page
- [ ] Terms of Service page
- [ ] Cookie Policy page

---

## Data Model (key tables)

| Table | Purpose |
|-------|---------|
| `countries` | Name, code, flag, tax type, DN visa flag |
| `tax_brackets` | Country → bracket rows (min, max, rate) |
| `residency_rules` | Country → threshold days + extra rules |
| `tax_treaties` | Country pair → credit type |
| `settings` | FEIE amount, physical-presence days, year |
| `user_calculations` | Saved calculation snapshots |
| `blog_posts` | Blog articles |
| `country_guides` | Guide page content per country |
| `analytics_events` | Custom event log |

---

*Last updated: 2026-02-12*
