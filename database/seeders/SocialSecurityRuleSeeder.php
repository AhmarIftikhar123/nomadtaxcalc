<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialSecurityRuleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $countries = DB::table('countries')->pluck('id', 'iso_code')->toArray();
        $rules = [];

        // Helper to add employee + employer pair
        $addPair = function (string $iso, int $year, string $fund, float $employeeRate, float $employerRate, ?float $maxIncome, ?float $annualCap, string $currency) use ($countries, $now, &$rules) {
            if (!isset($countries[$iso])) return;
            foreach (['employee' => $employeeRate, 'employer' => $employerRate] as $type => $rate) {
                if ($rate <= 0) continue;
                $rules[] = [
                    'country_id'        => $countries[$iso],
                    'tax_year'          => $year,
                    'contribution_type' => $type,
                    'fund_name'         => $fund,
                    'rate'              => $rate,
                    'min_income'        => 0,
                    'max_income'        => $maxIncome,
                    'annual_cap'        => $annualCap,
                    'currency_code'     => $currency,
                    'is_active'         => true,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        };

        // ──────────────────────────────────────────────────────────────────
        // UNITED STATES — FICA
        // Source: https://www.ssa.gov/oact/cola/cbb.html
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $ssCap = $year === 2025 ? 176100 : 180000; // 2026 projected
            // Social Security (OASDI)
            $addPair('US', $year, 'FICA - Social Security', 6.20, 6.20, $ssCap, null, 'USD');
            // Medicare (no cap)
            $addPair('US', $year, 'FICA - Medicare', 1.45, 1.45, null, null, 'USD');
            // Additional Medicare (employee only, on income > $200k)
            if (isset($countries['US'])) {
                $rules[] = [
                    'country_id'        => $countries['US'],
                    'tax_year'          => $year,
                    'contribution_type' => 'employee',
                    'fund_name'         => 'Additional Medicare',
                    'rate'              => 0.90,
                    'min_income'        => 200000,
                    'max_income'        => null,
                    'annual_cap'        => null,
                    'currency_code'     => 'USD',
                    'is_active'         => true,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        // ──────────────────────────────────────────────────────────────────
        // UNITED KINGDOM — National Insurance Contributions
        // Source: https://www.gov.uk/national-insurance-rates-letters
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            // Employee NIC Class 1 (8% on £12,570-£50,270, then 2% above)
            if (isset($countries['GB'])) {
                $rules[] = [
                    'country_id'        => $countries['GB'],
                    'tax_year'          => $year,
                    'contribution_type' => 'employee',
                    'fund_name'         => 'NIC Class 1 (Main)',
                    'rate'              => 8.00,
                    'min_income'        => 12570,
                    'max_income'        => 50270,
                    'annual_cap'        => null,
                    'currency_code'     => 'GBP',
                    'is_active'         => true,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
                $rules[] = [
                    'country_id'        => $countries['GB'],
                    'tax_year'          => $year,
                    'contribution_type' => 'employee',
                    'fund_name'         => 'NIC Class 1 (Additional)',
                    'rate'              => 2.00,
                    'min_income'        => 50270,
                    'max_income'        => null,
                    'annual_cap'        => null,
                    'currency_code'     => 'GBP',
                    'is_active'         => true,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
                // Employer NIC
                $rules[] = [
                    'country_id'        => $countries['GB'],
                    'tax_year'          => $year,
                    'contribution_type' => 'employer',
                    'fund_name'         => 'NIC Class 1 (Employer)',
                    'rate'              => 13.80,
                    'min_income'        => 9100,
                    'max_income'        => null,
                    'annual_cap'        => null,
                    'currency_code'     => 'GBP',
                    'is_active'         => true,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        // ──────────────────────────────────────────────────────────────────
        // GERMANY — Sozialversicherung
        // Source: https://www.deutsche-rentenversicherung.de
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $pensionCap = $year === 2025 ? 96600 : 99000;
            $healthCap  = $year === 2025 ? 69300 : 71100;
            // Pension Insurance (Rentenversicherung)
            $addPair('DE', $year, 'Pension Insurance', 9.30, 9.30, $pensionCap, null, 'EUR');
            // Health Insurance (Krankenversicherung) — avg rate
            $addPair('DE', $year, 'Health Insurance', 7.30, 7.30, $healthCap, null, 'EUR');
            // Unemployment Insurance (Arbeitslosenversicherung)
            $addPair('DE', $year, 'Unemployment Insurance', 1.30, 1.30, $pensionCap, null, 'EUR');
            // Long-term Care Insurance (Pflegeversicherung)
            $addPair('DE', $year, 'Long-term Care Insurance', 1.70, 1.70, $healthCap, null, 'EUR');
        }

        // ──────────────────────────────────────────────────────────────────
        // FRANCE — Cotisations sociales
        // Source: https://www.urssaf.fr
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $plafond = $year === 2025 ? 47100 : 48300; // Plafond annuel SS
            // CSG (Contribution Sociale Généralisée)
            $addPair('FR', $year, 'CSG', 9.20, 0, null, null, 'EUR');
            // CRDS
            $addPair('FR', $year, 'CRDS', 0.50, 0, null, null, 'EUR');
            // Old-age insurance (Assurance Vieillesse)
            $addPair('FR', $year, 'Old-age Insurance (Basic)', 6.90, 8.55, $plafond, null, 'EUR');
            $addPair('FR', $year, 'Old-age Insurance (Uncapped)', 0.40, 1.90, null, null, 'EUR');
            // Unemployment
            $addPair('FR', $year, 'Unemployment Insurance', 0, 4.05, $plafond * 4, null, 'EUR');
        }

        // ──────────────────────────────────────────────────────────────────
        // SPAIN — Seguridad Social
        // Source: https://www.seg-social.es
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('ES', $year, 'General Contingencies', 4.70, 23.60, $year === 2025 ? 56844 : 58000, null, 'EUR');
            $addPair('ES', $year, 'Unemployment', 1.55, 5.50, $year === 2025 ? 56844 : 58000, null, 'EUR');
            $addPair('ES', $year, 'Training', 0.10, 0.60, $year === 2025 ? 56844 : 58000, null, 'EUR');
        }

        // ──────────────────────────────────────────────────────────────────
        // ITALY — INPS Contributions
        // Source: https://www.inps.it
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('IT', $year, 'INPS Pension', 9.19, 23.81, 119650, null, 'EUR');
        }

        // ──────────────────────────────────────────────────────────────────
        // NETHERLANDS — Volksverzekeringen
        // Source: https://www.belastingdienst.nl
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            // Employee social insurance premiums (WW, WIA)
            $addPair('NL', $year, 'Social Insurance (Employee)', 0, 0, null, null, 'EUR');
            // National insurance (AOW, ANW, Wlz) — built into tax brackets
            if (isset($countries['NL'])) {
                $rules[] = [
                    'country_id'        => $countries['NL'],
                    'tax_year'          => $year,
                    'contribution_type' => 'employee',
                    'fund_name'         => 'National Insurance (AOW/ANW/Wlz)',
                    'rate'              => 27.65,
                    'min_income'        => 0,
                    'max_income'        => 38441,
                    'annual_cap'        => null,
                    'currency_code'     => 'EUR',
                    'is_active'         => true,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        // ──────────────────────────────────────────────────────────────────
        // PORTUGAL — Segurança Social
        // Source: https://www.seg-social.pt
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('PT', $year, 'Social Security', 11.00, 23.75, null, null, 'EUR');
        }

        // ──────────────────────────────────────────────────────────────────
        // JAPAN — Social Insurance
        // Source: https://www.nenkin.go.jp
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            // Kousei Nenkin (Employees' Pension)
            $addPair('JP', $year, 'Pension Insurance', 9.15, 9.15, null, null, 'JPY');
            // Health Insurance (avg rate)
            $addPair('JP', $year, 'Health Insurance', 5.00, 5.00, null, null, 'JPY');
            // Employment Insurance
            $addPair('JP', $year, 'Employment Insurance', 0.60, 0.95, null, null, 'JPY');
        }

        // ──────────────────────────────────────────────────────────────────
        // AUSTRALIA — Superannuation (Employer only)
        // Source: https://www.ato.gov.au
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('AU', $year, 'Superannuation Guarantee', 0, $year === 2025 ? 11.50 : 12.00, null, null, 'AUD');
            // Medicare Levy (employee only, modeled as social contribution)
            if (isset($countries['AU'])) {
                $rules[] = [
                    'country_id'        => $countries['AU'],
                    'tax_year'          => $year,
                    'contribution_type' => 'employee',
                    'fund_name'         => 'Medicare Levy',
                    'rate'              => 2.00,
                    'min_income'        => 26000, // Reduced threshold for singles
                    'max_income'        => null,
                    'annual_cap'        => null,
                    'currency_code'     => 'AUD',
                    'is_active'         => true,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        // ──────────────────────────────────────────────────────────────────
        // CANADA — CPP/EI
        // Source: https://www.canada.ca/en/revenue-agency
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $cppMax = $year === 2025 ? 71300 : 73200;
            // CPP (Canada Pension Plan)
            $addPair('CA', $year, 'CPP (Canada Pension Plan)', 5.95, 5.95, $cppMax, null, 'CAD');
            // EI (Employment Insurance)
            $addPair('CA', $year, 'EI (Employment Insurance)', 1.64, 2.30, $year === 2025 ? 65700 : 67500, null, 'CAD');
        }

        // ──────────────────────────────────────────────────────────────────
        // SOUTH KOREA — National Insurance
        // Source: https://www.nps.or.kr
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('KR', $year, 'National Pension', 4.50, 4.50, null, null, 'KRW');
            $addPair('KR', $year, 'Health Insurance', 3.545, 3.545, null, null, 'KRW');
            $addPair('KR', $year, 'Employment Insurance', 0.90, 1.15, null, null, 'KRW');
        }

        // ──────────────────────────────────────────────────────────────────
        // INDIA — EPF/ESI
        // Source: https://www.epfindia.gov.in
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('IN', $year, 'EPF (Provident Fund)', 12.00, 12.00, 1800000, null, 'INR');
            $addPair('IN', $year, 'ESI (State Insurance)', 0.75, 3.25, 252000, null, 'INR');
        }

        // ──────────────────────────────────────────────────────────────────
        // BRAZIL — INSS
        // Source: https://www.gov.br/previdencia
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            // Simplified: avg employee rate
            $addPair('BR', $year, 'INSS (Social Security)', 11.00, 20.00, $year === 2025 ? 96012 : 100000, null, 'BRL');
        }

        // ──────────────────────────────────────────────────────────────────
        // SWITZERLAND — AHV/IV/EO
        // Source: https://www.bsv.admin.ch
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('CH', $year, 'AHV/IV/EO', 5.30, 5.30, null, null, 'CHF');
            $addPair('CH', $year, 'Occupational Pension (BVG)', 7.00, 7.00, 88200, null, 'CHF'); // Mandatory minimum
            $addPair('CH', $year, 'Unemployment Insurance', 1.10, 1.10, 148200, null, 'CHF');
        }

        // ──────────────────────────────────────────────────────────────────
        // AUSTRIA — Sozialversicherung
        // Source: https://www.sozialversicherung.at
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('AT', $year, 'Pension Insurance', 10.25, 12.55, 75180, null, 'EUR');
            $addPair('AT', $year, 'Health Insurance', 3.87, 3.78, 75180, null, 'EUR');
            $addPair('AT', $year, 'Unemployment Insurance', 3.00, 3.00, 75180, null, 'EUR');
        }

        // ──────────────────────────────────────────────────────────────────
        // IRELAND — PRSI
        // Source: https://www.revenue.ie
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('IE', $year, 'PRSI (Class A)', 4.00, 11.05, null, null, 'EUR');
        }

        // ──────────────────────────────────────────────────────────────────
        // SWEDEN — Socialavgifter
        // Source: https://www.skatteverket.se
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            // Employee pays only 7% special payroll tax contribution
            $addPair('SE', $year, 'General Pension Contribution', 7.00, 0, 614000, null, 'SEK');
            // Employer pays socialavgifter (arbetsgivaravgifter) 31.42%
            $addPair('SE', $year, 'Employer Social Contributions', 0, 31.42, null, null, 'SEK');
        }

        // ──────────────────────────────────────────────────────────────────
        // POLAND — ZUS
        // Source: https://www.zus.pl
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $zusCap = $year === 2025 ? 260190 : 270000;
            $addPair('PL', $year, 'Pension (Emerytalna)', 9.76, 9.76, $zusCap, null, 'PLN');
            $addPair('PL', $year, 'Disability (Rentowa)', 1.50, 6.50, $zusCap, null, 'PLN');
            $addPair('PL', $year, 'Sickness (Chorobowa)', 2.45, 0, null, null, 'PLN');
            $addPair('PL', $year, 'Health Insurance', 9.00, 0, null, null, 'PLN');
        }

        // ──────────────────────────────────────────────────────────────────
        // HUNGARY — Social Contribution (SZOCHO + TB)
        // Source: https://nav.gov.hu
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('HU', $year, 'Social Contribution Tax (TB)', 18.50, 0, null, null, 'HUF');
            $addPair('HU', $year, 'Social Contribution (SZOCHO)', 0, 13.00, null, null, 'HUF');
        }

        // ──────────────────────────────────────────────────────────────────
        // CZECH REPUBLIC — Social Insurance
        // Source: https://www.cssz.cz
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $czCap = $year === 2025 ? 2234736 : 2300000;
            $addPair('CZ', $year, 'Social Insurance', 6.50, 24.80, $czCap, null, 'CZK');
            $addPair('CZ', $year, 'Health Insurance', 4.50, 9.00, null, null, 'CZK');
        }

        // ──────────────────────────────────────────────────────────────────
        // SINGAPORE — CPF
        // Source: https://www.cpf.gov.sg
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            // Rates for age ≤ 55
            $addPair('SG', $year, 'CPF (Central Provident Fund)', 20.00, 17.00, 102000, null, 'SGD');
        }

        // ──────────────────────────────────────────────────────────────────
        // CHINA — Social Insurance (Five Insurances)
        // Source: Various provincial sources
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('CN', $year, 'Pension Insurance', 8.00, 16.00, null, null, 'CNY');
            $addPair('CN', $year, 'Medical Insurance', 2.00, 8.00, null, null, 'CNY');
            $addPair('CN', $year, 'Unemployment Insurance', 0.50, 0.50, null, null, 'CNY');
            $addPair('CN', $year, 'Housing Fund', 5.00, 5.00, null, null, 'CNY');
        }

        // ──────────────────────────────────────────────────────────────────
        // THAILAND — Social Security
        // Source: https://www.sso.go.th
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('TH', $year, 'Social Security Fund', 5.00, 5.00, 180000, 9000, 'THB');
        }

        // ──────────────────────────────────────────────────────────────────
        // COLOMBIA — Social Security
        // Source: https://www.dian.gov.co
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('CO', $year, 'Pension', 4.00, 12.00, null, null, 'COP');
            $addPair('CO', $year, 'Health', 4.00, 8.50, null, null, 'COP');
        }

        // ──────────────────────────────────────────────────────────────────
        // PAKISTAN — EOBI
        // Source: https://www.eobi.gov.pk
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('PK', $year, 'EOBI (Old Age Benefits)', 1.00, 5.00, null, null, 'PKR');
        }

        // ──────────────────────────────────────────────────────────────────
        // NORWAY — Trygdeavgift
        // Source: https://www.skatteetaten.no
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('NO', $year, 'National Insurance (Trygdeavgift)', 7.80, 14.10, null, null, 'NOK');
        }

        // ──────────────────────────────────────────────────────────────────
        // DENMARK — AM-bidrag
        // Source: https://www.skat.dk
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            // AM-bidrag (Labour Market Contribution)
            $addPair('DK', $year, 'AM-bidrag (Labour Market)', 8.00, 0, null, null, 'DKK');
            // ATP (Supplementary Labour Market Pension) — flat monthly
            $addPair('DK', $year, 'Employer Social Contributions', 0, 0, null, null, 'DKK');
        }

        // ──────────────────────────────────────────────────────────────────
        // FINLAND — Social Insurance
        // Source: https://www.vero.fi
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('FI', $year, 'Pension Insurance (TyEL)', 7.15, 17.15, null, null, 'EUR');
            $addPair('FI', $year, 'Unemployment Insurance', 1.50, 0.52, null, null, 'EUR');
            $addPair('FI', $year, 'Health Insurance', 1.96, 1.53, null, null, 'EUR');
        }

        // ──────────────────────────────────────────────────────────────────
        // BELGIUM — Social Security
        // Source: https://www.rsz.be
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('BE', $year, 'Social Security', 13.07, 27.00, null, null, 'EUR');
        }

        // ──────────────────────────────────────────────────────────────────
        // TURKEY — SGK
        // Source: https://www.sgk.gov.tr
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('TR', $year, 'SSI (Social Security)', 14.00, 20.50, null, null, 'TRY');
            $addPair('TR', $year, 'Unemployment Insurance', 1.00, 2.00, null, null, 'TRY');
        }

        // ──────────────────────────────────────────────────────────────────
        // ISRAEL — Bituach Leumi
        // Source: https://www.btl.gov.il
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            // Reduced rate up to ~60% of avg wage, full rate above
            $addPair('IL', $year, 'National Insurance (Bituach Leumi)', 3.50, 3.55, null, null, 'ILS');
            $addPair('IL', $year, 'Health Insurance', 3.10, 0, null, null, 'ILS');
        }

        // ──────────────────────────────────────────────────────────────────
        // ROMANIA — CAS/CASS
        // Source: https://www.anaf.ro
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('RO', $year, 'Pension Insurance (CAS)', 25.00, 0, null, null, 'RON');
            $addPair('RO', $year, 'Health Insurance (CASS)', 10.00, 0, null, null, 'RON');
        }

        // ──────────────────────────────────────────────────────────────────
        // CROATIA — Doprinosi
        // Source: https://www.porezna-uprava.hr
        // ──────────────────────────────────────────────────────────────────
        foreach ([2025, 2026] as $year) {
            $addPair('HR', $year, 'Pension Insurance (I Pillar)', 15.00, 0, null, null, 'EUR');
            $addPair('HR', $year, 'Pension Insurance (II Pillar)', 5.00, 0, null, null, 'EUR');
            $addPair('HR', $year, 'Health Insurance', 0, 16.50, null, null, 'EUR');
        }

        // Insert all rules in chunks
        foreach (array_chunk($rules, 50) as $chunk) {
            DB::table('social_security_rules')->insert($chunk);
        }
    }
}
