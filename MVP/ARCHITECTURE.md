# NomadTax MVP — Architecture & Flow Diagrams

> Every diagram below is **valid Mermaid** syntax.
> Paste any block into [mermaid.live](https://mermaid.live) to verify.

---

## 1. High-Level System Architecture

```mermaid
flowchart TB
    subgraph Client["Frontend - React / Inertia.js"]
        LP["Landing Page"]
        CALC["Tax Calculator Wizard"]
        RES["Results Page"]
        COMP["Country Comparison"]
        GUIDE["Country Guide Pages"]
        BLOG["Blog System"]
    end

    subgraph Server["Backend - Laravel"]
        CTRL["Controllers"]
        SVC["Service Layer"]
        MDL["Eloquent Models"]
    end

    subgraph Services["Core Services"]
        TAXSVC["TaxCalculatorService"]
        FEIESVC["FEIEService"]
        RISKSVC["RiskAssessmentService"]
        RECSVC["RecommendationService"]
        COMPSVC["ComparisonService"]
    end

    subgraph DataStore["Database - MySQL"]
        COUNTRIES["countries"]
        BRACKETS["tax_brackets"]
        RULES["residency_rules"]
        TREATIES["tax_treaties"]
        SETTINGS["settings"]
        USERCALC["user_calculations"]
        POSTS["blog_posts"]
        GUIDES["country_guides"]
        EVENTS["analytics_events"]
    end

    subgraph External["External Integrations"]
        GA["Google Analytics 4"]
        ADS["Google AdSense"]
        AFF["Affiliate Partners"]
    end

    Client -->|"Inertia requests"| CTRL
    CTRL --> SVC
    SVC --> TAXSVC
    SVC --> FEIESVC
    SVC --> RISKSVC
    SVC --> RECSVC
    SVC --> COMPSVC
    TAXSVC --> MDL
    FEIESVC --> MDL
    RISKSVC --> MDL
    RECSVC --> MDL
    COMPSVC --> MDL
    MDL --> DataStore
    Client -->|"Events"| GA
    Client -->|"Ad slots"| ADS
    Client -->|"Clicks"| AFF

    style Client fill:#1e293b,color:#e2e8f0,stroke:#3b82f6
    style Server fill:#1e293b,color:#e2e8f0,stroke:#8b5cf6
    style Services fill:#1e293b,color:#e2e8f0,stroke:#f59e0b
    style DataStore fill:#1e293b,color:#e2e8f0,stroke:#10b981
    style External fill:#1e293b,color:#e2e8f0,stroke:#ef4444
```

---

## 2. Multi-Country Tax Calculator — Full User Flow

```mermaid
flowchart TD
    START(["User visits homepage"]) --> LAND["Landing Page"]
    LAND --> CTA{"Clicks Try Calculator"}
    CTA --> S1["Step 1: Income and Citizenship"]

    S1 --> INPUT1["Enter annual income and citizenship"]
    INPUT1 --> V1{"Valid input?"}
    V1 -->|"No"| ERR1["Show error message"]
    ERR1 --> INPUT1
    V1 -->|"Yes"| SAVE1[("Save to session")]
    SAVE1 --> S2["Step 2: Countries Visited"]

    S2 --> ADD["Add country and days"]
    ADD --> LIST["Display country list"]
    LIST --> DAYCHECK{"Total days = 365?"}
    DAYCHECK -->|"No"| ADDMORE["Add more countries"]
    ADDMORE --> ADD
    DAYCHECK -->|"Yes"| RULE183{"Any country >= 183 days?"}
    RULE183 -->|"Yes"| WARN["Show tax-resident warning"]
    RULE183 -->|"No"| SAFE["Show all safe"]
    WARN --> SAVE2[("Save countries to session")]
    SAFE --> SAVE2
    SAVE2 --> S3["Step 3: Review and Calculate"]

    S3 --> SUMMARY["Display summary"]
    SUMMARY --> CALCBTN{"Click Calculate"}
    CALCBTN --> BACKEND["Backend Processing"]

    BACKEND --> FETCHDB[("Fetch tax brackets, residency rules, treaties, FEIE settings")]
    FETCHDB --> LOOP_COUNTRY["For each country"]
    LOOP_COUNTRY --> IS_RESIDENT{"Tax resident?"}
    IS_RESIDENT -->|"Yes"| CALC_TAX["Calculate progressive tax"]
    IS_RESIDENT -->|"No"| NEXT_C["Next country"]
    CALC_TAX --> HAS_TREATY{"Treaty exists?"}
    HAS_TREATY -->|"Yes"| APPLY_CREDIT["Apply foreign tax credit"]
    HAS_TREATY -->|"No"| RAW_TAX["Use raw tax amount"]
    APPLY_CREDIT --> NEXT_C
    RAW_TAX --> NEXT_C
    NEXT_C --> MORE_C{"More countries?"}
    MORE_C -->|"Yes"| LOOP_COUNTRY
    MORE_C -->|"No"| US_CHECK{"US citizen?"}

    US_CHECK -->|"Yes"| FEIE_CALC["Calculate FEIE exclusion"]
    US_CHECK -->|"No"| SKIP_FEIE["Skip FEIE"]
    FEIE_CALC --> FEIE_QUALIFY{"Qualifies for FEIE?"}
    FEIE_QUALIFY -->|"Yes"| APPLY_FEIE["Reduce US tax liability"]
    FEIE_QUALIFY -->|"No"| NO_FEIE["No FEIE applied"]
    APPLY_FEIE --> TOTAL["Calculate total tax"]
    NO_FEIE --> TOTAL
    SKIP_FEIE --> TOTAL

    TOTAL --> GEN_REC["Generate recommendations"]
    GEN_REC --> SAVE_CALC[("Save to user_calculations")]
    SAVE_CALC --> TRACK[("Track analytics event")]
    TRACK --> RESULTS["Display Results Page"]

    RESULTS --> BREAKDOWN["Tax breakdown per country"]
    BREAKDOWN --> CHART["Comparison chart"]
    CHART --> WARNINGS["Residency warnings"]
    WARNINGS --> RECS["Recommendations"]
    RECS --> AFFILIATES["Affiliate links"]
    AFFILIATES --> ADSLOT["Google Ads"]

    ADSLOT --> ACTION{"User action?"}
    ACTION -->|"Adjust inputs"| S1
    ACTION -->|"View guide"| GUIDEPAGE["Country Tax Guide"]
    ACTION -->|"Click affiliate"| TRACK_AFF[("Track affiliate click")]
    ACTION -->|"Share"| SHARE["Generate shareable URL"]
    ACTION -->|"Done"| FINISH(["Session ends"])
    TRACK_AFF --> EXTSITE["Redirect to partner"]
    EXTSITE --> FINISH
    GUIDEPAGE --> FINISH
    SHARE --> FINISH

    style START fill:#16a34a,color:#fff
    style FINISH fill:#dc2626,color:#fff
    style BACKEND fill:#2563eb,color:#fff
    style RESULTS fill:#ea580c,color:#fff
```

---

## 3. 183-Day Rule Tracker — Flow

```mermaid
flowchart TD
    IN(["User enters days in country"]) --> INPUT["Country: Spain, Days: 150"]

    INPUT --> FETCH[("Fetch residency rules")]
    FETCH --> THRESHOLD["Get threshold: 183 days"]

    THRESHOLD --> CALC["Calculate: remaining = 183 - 150 = 33"]

    CALC --> RISK{"Determine risk level"}
    RISK -->|"Days less than 150"| GREEN["GREEN: Safe"]
    RISK -->|"150 to 182 days"| YELLOW["YELLOW: Warning"]
    RISK -->|"183 or more days"| RED["RED: Tax Resident"]

    GREEN --> DISP_G["Progress bar green"]
    YELLOW --> DISP_Y["Progress bar yellow 82 percent"]
    RED --> DISP_R["Progress bar red, over threshold"]

    DISP_G --> VISUAL["Show visual indicator"]
    DISP_Y --> VISUAL
    DISP_R --> VISUAL

    VISUAL --> ADDLIST["Add to country list with colour"]
    ADDLIST --> TOTAL{"Total days = 365?"}
    TOTAL -->|"No"| ALLOW["Allow adding more"]
    TOTAL -->|"Yes"| LOCK["Lock list, show Continue"]

    ALLOW --> USERADD{"Add another country?"}
    USERADD -->|"Yes"| IN
    USERADD -->|"No"| WAIT["Wait for user"]
    WAIT --> ADJUST{"Adjust days?"}
    ADJUST -->|"Yes"| IN
    ADJUST -->|"No"| LOCK

    LOCK --> CONTINUE["Continue to next step"]
    CONTINUE --> DONE(["Proceed to Results"])

    style IN fill:#16a34a,color:#fff
    style GREEN fill:#16a34a,color:#fff
    style YELLOW fill:#eab308,color:#000
    style RED fill:#dc2626,color:#fff
    style DONE fill:#6b7280,color:#fff
```

---

## 4. FEIE Calculator — Flow

```mermaid
flowchart TD
    BEGIN(["Calculation starts"]) --> IS_US{"Citizenship = USA?"}

    IS_US -->|"No"| SKIP["Skip FEIE"]
    IS_US -->|"Yes"| FETCH_S[("Fetch FEIE settings")]

    FETCH_S --> GET_AMT["FEIE limit: 130000 USD for 2026"]
    GET_AMT --> GET_DAYS["Required: 330 days outside US"]
    GET_DAYS --> COUNT["Count days outside US"]
    COUNT --> CALC_D["Total outside = 365, US days = 0"]

    CALC_D --> PPT{"Physical Presence Test: outside >= 330?"}
    PPT -->|"No"| FAIL["Does NOT qualify"]
    PPT -->|"Yes"| PASS["Qualifies for FEIE"]

    FAIL --> NO_EXCL["No exclusion, full US tax"]
    NO_EXCL --> CALC_FULL["Calculate US tax with standard brackets"]

    PASS --> INCOME_CHK{"Income above FEIE limit?"}
    INCOME_CHK -->|"No"| FULL_EXCL["Full income excluded"]
    INCOME_CHK -->|"Yes"| PARTIAL["Partial exclusion, 130000 excluded"]

    FULL_EXCL --> ZERO_TAX["US taxable income: 0"]
    PARTIAL --> CALC_REM["Tax on income minus 130000"]

    ZERO_TAX --> DISPLAY["Display on Results Page"]
    CALC_REM --> DISPLAY
    CALC_FULL --> DISPLAY

    DISPLAY --> BADGE["Show FEIE badge"]
    BADGE --> DETAILS["Show exclusion details"]
    DETAILS --> EXPLAIN["Explain FEIE and Form 2555"]
    EXPLAIN --> LINK_GUIDE["Link to FEIE Guide 2026"]
    LINK_GUIDE --> AFF_OFFER["Affiliate: tax filing services"]

    AFF_OFFER --> ENDFEIE(["Continue to full results"])
    SKIP --> ENDFEIE

    style BEGIN fill:#16a34a,color:#fff
    style PASS fill:#16a34a,color:#fff
    style FAIL fill:#dc2626,color:#fff
    style ZERO_TAX fill:#16a34a,color:#fff
    style DISPLAY fill:#2563eb,color:#fff
    style ENDFEIE fill:#6b7280,color:#fff
```

---

## 5. Country Comparison Tool — Flow

```mermaid
flowchart TD
    OPEN(["User on Results Page"]) --> CLICK["Click Compare Countries"]
    CLICK --> MODAL["Open comparison modal"]
    MODAL --> LOAD["Load current countries"]

    LOAD --> SELECTOR["Show country selector"]
    SELECTOR --> ADD_Q{"Add more countries?"}
    ADD_Q -->|"Yes"| PICK["Add UAE, Estonia"]
    ADD_Q -->|"No"| USE_EXIST["Use existing only"]

    PICK --> FETCH_ALL[("Fetch tax data for each country")]
    USE_EXIST --> FETCH_ALL

    FETCH_ALL --> LOOP["For each country"]
    LOOP --> CALC_CMP["Calculate tax for income"]
    CALC_CMP --> ASSUME["Assume tax resident 183 plus days"]
    ASSUME --> APPLY_B["Apply tax brackets"]
    APPLY_B --> EFF_RATE["Calculate effective rate"]
    EFF_RATE --> STORE[("Store result")]

    STORE --> MORE_CMP{"More countries?"}
    MORE_CMP -->|"Yes"| LOOP
    MORE_CMP -->|"No"| SORT["Sort by tax amount ascending"]

    SORT --> BUILD_CHART["Build horizontal bar chart"]
    BUILD_CHART --> META["Add metadata: visa, income req, cost of living"]
    META --> RENDER_CHART["Display interactive chart"]

    RENDER_CHART --> BEST["Highlight best: UAE 0 USD"]
    BEST --> TABLE["Show detailed comparison table"]

    TABLE --> CMP_ACT{"User action?"}
    CMP_ACT -->|"View guide"| OPEN_GUIDE["Open country guide"]
    CMP_ACT -->|"Change income"| NEW_INCOME["Enter new income"]
    CMP_ACT -->|"Add country"| SELECTOR
    CMP_ACT -->|"Close"| CLOSE["Close modal"]

    OPEN_GUIDE --> TRACK_INT[("Track interest")]
    TRACK_INT --> NAV_GUIDE["Navigate to guide"]
    NEW_INCOME --> LOOP

    CLOSE --> ENDCMP(["Return to Results"])
    NAV_GUIDE --> ENDCMP

    style OPEN fill:#16a34a,color:#fff
    style RENDER_CHART fill:#2563eb,color:#fff
    style BEST fill:#eab308,color:#000
    style ENDCMP fill:#6b7280,color:#fff
```

---

## 6. Tax Residency Risk Assessment — Flow

```mermaid
flowchart TD
    RSTART(["Risk assessment starts"]) --> GET_C["Get user countries"]

    GET_C --> FETCH_R[("Fetch residency rules")]
    FETCH_R --> LOOP_R["For each country"]

    LOOP_R --> GET_TH["Get threshold days"]
    GET_TH --> GET_UD["Get user days"]
    GET_UD --> CMP_DAYS{"Days >= threshold?"}

    CMP_DAYS -->|"Yes"| HIGH["HIGH RISK: Tax Resident"]
    CMP_DAYS -->|"No"| CLOSE_R{"Days >= threshold minus 33?"}
    CLOSE_R -->|"Yes"| MEDIUM["MEDIUM RISK: Approaching"]
    CLOSE_R -->|"No"| LOW["LOW RISK: Safe"]

    HIGH --> DAYS_OVER["Calculate days over threshold"]
    DAYS_OVER --> WARN_HIGH["Warning: You ARE a tax resident"]

    MEDIUM --> DAYS_LEFT["Calculate days remaining"]
    DAYS_LEFT --> WARN_MED["Warning: Approaching threshold"]

    LOW --> MSG_SAFE["Message: Safe, no risk"]

    WARN_HIGH --> EXTRA{"Additional residency rules?"}
    WARN_MED --> EXTRA
    MSG_SAFE --> EXTRA

    EXTRA -->|"Vital interests or home test"| ADD_NOTE["Add extra rule note"]
    EXTRA -->|"None"| SKIP_NOTE["No extra notes"]

    ADD_NOTE --> STORE_R[("Store risk assessment")]
    SKIP_NOTE --> STORE_R

    STORE_R --> MORE_R{"More countries?"}
    MORE_R -->|"Yes"| LOOP_R
    MORE_R -->|"No"| GEN_RECS["Generate recommendations"]

    GEN_RECS --> ANY_HIGH{"Any high-risk country?"}
    ANY_HIGH -->|"Yes"| REC_REDUCE["Recommend: reduce days below 183"]
    ANY_HIGH -->|"No"| REC_MAINTAIN["Recommend: maintain pattern"]

    REC_REDUCE --> REC_DOC["Recommend: keep documentation"]
    REC_MAINTAIN --> REC_DOC
    REC_DOC --> REC_CONSULT["Recommend: consult tax professional"]

    REC_CONSULT --> BUILD_CARD["Build risk assessment card"]
    BUILD_CARD --> SORT_RISK["Sort: high risk first"]
    SORT_RISK --> COLOURS["Add colour badges"]
    COLOURS --> DISPLAY_R["Display on results page"]

    DISPLAY_R --> LINK_GUIDES["Link to 183-Day Rule article"]
    LINK_GUIDES --> ENDRISK(["Assessment complete"])

    style RSTART fill:#16a34a,color:#fff
    style HIGH fill:#dc2626,color:#fff
    style MEDIUM fill:#eab308,color:#000
    style LOW fill:#16a34a,color:#fff
    style DISPLAY_R fill:#2563eb,color:#fff
    style ENDRISK fill:#6b7280,color:#fff
```

---

## 7. Country Tax Guide Page — SEO Flow

```mermaid
flowchart TD
    SEARCH(["User searches Google"]) --> SERP["Google shows our guide page"]

    SERP --> CLICK_R{"User clicks our result?"}
    CLICK_R -->|"No"| LOST["User lost"]
    CLICK_R -->|"Yes"| TRACK_SRC[("Track referral source")]

    TRACK_SRC --> LOAD_PG["Load /country/slug-tax-guide"]
    LOAD_PG --> FETCH_CG[("Fetch country data, tax brackets, visa info, blog posts")]

    FETCH_CG --> BUILD_PG["Build dynamic page"]
    BUILD_PG --> HERO["Render hero section"]
    HERO --> CTA_CALC["Render Calculate CTA"]
    CTA_CALC --> OVERVIEW["Render tax overview table"]
    OVERVIEW --> VISA["Render DN visa section"]
    VISA --> RESIDENCY["Render 183-Day Rule"]
    RESIDENCY --> US_SECTION["Render US citizen section"]
    US_SECTION --> COL["Render cost of living"]
    COL --> PROS_CONS["Render pros and cons"]
    PROS_CONS --> RELATED["Render related articles"]
    RELATED --> AFF_ADS["Render affiliate links and ads"]

    AFF_ADS --> USER_ACT{"User action?"}
    USER_ACT -->|"Calculator"| PREFILL["Pre-fill calculator with country"]
    USER_ACT -->|"Blog post"| BLOG_PG["Navigate to blog post"]
    USER_ACT -->|"Affiliate"| TRACK_CLK[("Track affiliate click")]
    USER_ACT -->|"Leave"| EXITG(["Session ends"])

    PREFILL --> CALC_FLOW["Enter calculator flow"]
    BLOG_PG --> EXITG
    TRACK_CLK --> PARTNER["Redirect to partner"]
    PARTNER --> EXITG
    CALC_FLOW --> EXITG

    style SEARCH fill:#16a34a,color:#fff
    style LOAD_PG fill:#2563eb,color:#fff
    style EXITG fill:#6b7280,color:#fff
```

---

## 8. Blog System — Content Flow

```mermaid
flowchart TD
    ENTRY(["User enters blog"]) --> INDEX["Blog index page"]

    INDEX --> FILTER["Filter by category"]
    FILTER --> LIST["Display post cards with pagination"]

    LIST --> CLICK_P{"Click a post?"}
    CLICK_P -->|"No"| PAGINATE["Next page"]
    PAGINATE --> LIST
    CLICK_P -->|"Yes"| LOAD_POST["Load article page"]

    LOAD_POST --> FETCH_POST[("Fetch post content, author, related posts")]
    FETCH_POST --> RENDER_ART["Render article"]
    RENDER_ART --> HERO_ART["Hero image and title"]
    HERO_ART --> BODY["Article body"]
    BODY --> AUTHOR["Author and date"]
    AUTHOR --> RELATED_P["Related posts sidebar"]
    RELATED_P --> IN_ADS["In-content ad slots"]
    IN_ADS --> CTA_BLOG["CTA: Try Calculator"]

    CTA_BLOG --> BLOG_ACT{"User action?"}
    BLOG_ACT -->|"Calculator"| GO_CALC["Navigate to calculator"]
    BLOG_ACT -->|"Related post"| LOAD_POST
    BLOG_ACT -->|"Guide page"| GO_GUIDE["Navigate to country guide"]
    BLOG_ACT -->|"Leave"| EXITB(["Session ends"])

    GO_CALC --> EXITB
    GO_GUIDE --> EXITB

    style ENTRY fill:#16a34a,color:#fff
    style RENDER_ART fill:#2563eb,color:#fff
    style EXITB fill:#6b7280,color:#fff
```

---

## 9. Complete MVP Data Model — ER Diagram

```mermaid
erDiagram
    COUNTRIES {
        int id PK
        string name
        string code
        string flag_emoji
        string tax_type
        boolean has_dn_visa
        decimal min_income_req
        decimal cost_of_living_monthly
    }

    TAX_BRACKETS {
        int id PK
        int country_id FK
        decimal min_income
        decimal max_income
        decimal rate_percent
        int year
    }

    RESIDENCY_RULES {
        int id PK
        int country_id FK
        int threshold_days
        string additional_rules
        string description
    }

    TAX_TREATIES {
        int id PK
        int country_a_id FK
        int country_b_id FK
        string credit_type
        string notes
    }

    SETTINGS {
        int id PK
        string key
        string value
        string description
        int year
    }

    USER_CALCULATIONS {
        int id PK
        string session_id
        decimal annual_income
        string citizenship_country
        json countries_visited
        json tax_breakdown
        decimal total_tax
        decimal effective_rate
        boolean feie_applied
        timestamp created_at
    }

    BLOG_POSTS {
        int id PK
        string title
        string slug
        text body
        string category
        string author
        string meta_description
        boolean is_published
        timestamp published_at
    }

    COUNTRY_GUIDES {
        int id PK
        int country_id FK
        string slug
        text content
        string meta_title
        string meta_description
        boolean is_published
    }

    ANALYTICS_EVENTS {
        int id PK
        string event_type
        string session_id
        json event_data
        string source
        timestamp created_at
    }

    COUNTRIES ||--o{ TAX_BRACKETS : "has many"
    COUNTRIES ||--o{ RESIDENCY_RULES : "has many"
    COUNTRIES ||--o| COUNTRY_GUIDES : "has one"
    COUNTRIES ||--o{ TAX_TREATIES : "country_a"
    COUNTRIES ||--o{ TAX_TREATIES : "country_b"
```

---

## 10. Overall MVP Feature Interaction Map

```mermaid
flowchart LR
    subgraph Entry["Entry Points"]
        HOMEPAGE["Homepage"]
        GOOGLE["Google Search"]
        DIRECT["Direct URL"]
    end

    subgraph Core["Core Features"]
        TAXCALC["Tax Calculator"]
        DAYTRACK["183-Day Tracker"]
        FEIE["FEIE Calculator"]
        RISKASSESS["Risk Assessment"]
    end

    subgraph Content["Content Features"]
        GUIDES["Country Guides"]
        BLOGF["Blog Articles"]
        COMPARE["Comparison Tool"]
    end

    subgraph Monetise["Monetisation"]
        ADSENSE["Google AdSense"]
        AFFILIATE["Affiliate Links"]
        ANALYTICS["Analytics"]
    end

    subgraph Output["Output"]
        RESULTSF["Results Page"]
    end

    HOMEPAGE --> TAXCALC
    GOOGLE --> GUIDES
    GOOGLE --> BLOGF
    DIRECT --> TAXCALC

    TAXCALC --> DAYTRACK
    TAXCALC --> FEIE
    TAXCALC --> RISKASSESS
    TAXCALC --> RESULTSF

    RESULTSF --> COMPARE
    RESULTSF --> GUIDES
    RESULTSF --> AFFILIATE
    RESULTSF --> ADSENSE

    GUIDES --> TAXCALC
    GUIDES --> BLOGF
    GUIDES --> AFFILIATE

    BLOGF --> TAXCALC
    BLOGF --> GUIDES
    BLOGF --> ADSENSE

    COMPARE --> GUIDES

    TAXCALC --> ANALYTICS
    RESULTSF --> ANALYTICS
    GUIDES --> ANALYTICS
    BLOGF --> ANALYTICS

    style Entry fill:#0f172a,color:#e2e8f0,stroke:#3b82f6
    style Core fill:#0f172a,color:#e2e8f0,stroke:#8b5cf6
    style Content fill:#0f172a,color:#e2e8f0,stroke:#f59e0b
    style Monetise fill:#0f172a,color:#e2e8f0,stroke:#10b981
    style Output fill:#0f172a,color:#e2e8f0,stroke:#ef4444
```
