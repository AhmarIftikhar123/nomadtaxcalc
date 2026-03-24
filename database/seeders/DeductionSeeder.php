<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeductionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Map country ISO codes to IDs
        $countries = DB::table('countries')->pluck('id', 'iso_code')->toArray();

        $deductions = [];

        // ──────────────────────────────────────────────────────────────────────
        // NORTH AMERICA
        // ──────────────────────────────────────────────────────────────────────

        // United States — IRS Rev. Proc. 2024-40 (2025) & projected 2026
        // Source: https://www.irs.gov/newsroom/irs-provides-tax-inflation-adjustments-for-tax-year-2025
        $usDeductions = [
            ['filing_status' => 'single',           'amount_2025' => 15000, 'amount_2026' => 15700],
            ['filing_status' => 'married_joint',     'amount_2025' => 30000, 'amount_2026' => 31400],
            ['filing_status' => 'married_separate',  'amount_2025' => 15000, 'amount_2026' => 15700],
            ['filing_status' => 'head_of_household', 'amount_2025' => 22500, 'amount_2026' => 23550],
        ];
        foreach ($usDeductions as $d) {
            foreach ([2025, 2026] as $year) {
                $deductions[] = [
                    'country_id'      => $countries['US'] ?? null,
                    'tax_year'        => $year,
                    'deduction_type'  => 'standard',
                    'filing_status'   => $d['filing_status'],
                    'amount'          => $d['amount_' . $year],
                    'is_percentage'   => false,
                    'phase_out_start' => null,
                    'phase_out_end'   => null,
                    'currency_code'   => 'USD',
                    'description'     => 'US Standard Deduction',
                    'is_active'       => true,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
        }

        // Canada — CRA Basic Personal Amount
        // Source: https://www.canada.ca/en/revenue-agency/services/tax/individuals/frequently-asked-questions-individuals/adjustment-personal-income-tax-benefit-amounts.html
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['CA'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => $year === 2025 ? 16129 : 16500,
                'is_percentage'   => false,
                'phase_out_start' => $year === 2025 ? 173205 : 177000,
                'phase_out_end'   => $year === 2025 ? 246752 : 252000,
                'currency_code'   => 'CAD',
                'description'     => 'Basic Personal Amount (BPA)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Mexico — No standard deduction, personal deductions are itemized
        // Included as zero for completeness
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['MX'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'standard',
                'filing_status'   => 'default',
                'amount'          => 0,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'MXN',
                'description'     => 'No standard deduction; itemized personal deductions available',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // ──────────────────────────────────────────────────────────────────────
        // EUROPE
        // ──────────────────────────────────────────────────────────────────────

        // United Kingdom — HMRC Personal Allowance
        // Source: https://www.gov.uk/income-tax-rates
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['GB'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 12570,
                'is_percentage'   => false,
                'phase_out_start' => 100000,
                'phase_out_end'   => 125140, // £1 lost per £2 over £100k
                'currency_code'   => 'GBP',
                'description'     => 'Personal Allowance (tapers above £100k)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Germany — §32a EStG Grundfreibetrag
        // Source: https://www.bzst.bund.de / §32a EStG
        $deData = ['2025' => 12084, '2026' => 12348];
        foreach ($deData as $year => $amount) {
            $deductions[] = [
                'country_id'      => $countries['DE'] ?? null,
                'tax_year'        => (int) $year,
                'deduction_type'  => 'basic_relief',
                'filing_status'   => 'default',
                'amount'          => $amount,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'EUR',
                'description'     => 'Grundfreibetrag (Basic tax-free allowance)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // France — 10% employment income deduction (automatic)
        // Source: https://www.impots.gouv.fr
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['FR'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'employment_income',
                'filing_status'   => 'default',
                'amount'          => 10,
                'is_percentage'   => true, // 10% of employment income
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'EUR',
                'description'     => '10% employment income deduction (min €495, max €14,171)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Spain — Reducción por rendimientos del trabajo
        // Source: https://www.agenciatributaria.es
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['ES'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'employment_income',
                'filing_status'   => 'default',
                'amount'          => 5565,
                'is_percentage'   => false,
                'phase_out_start' => 14852,
                'phase_out_end'   => 19747,
                'currency_code'   => 'EUR',
                'description'     => 'Minimum personal allowance + work income reduction',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Italy — Deduzione per lavoro dipendente / tax credits system
        // Source: https://www.agenziaentrate.gov.it
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['IT'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 8500,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'EUR',
                'description'     => 'No-tax area for employment income',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Netherlands — General tax credit (arbeidskorting is credit-based, not deduction)
        // Source: https://www.belastingdienst.nl
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['NL'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 0,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'EUR',
                'description'     => 'Tax credits system (heffingskorting) — modeled separately',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Portugal — Deducción específica
        // Source: https://www.portaldasfinancas.gov.pt
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['PT'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'employment_income',
                'filing_status'   => 'default',
                'amount'          => 4104,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'EUR',
                'description'     => 'Specific deduction for employment income',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Switzerland — Federal basic deduction
        // Source: https://www.estv.admin.ch
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['CH'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 0,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'CHF',
                'description'     => 'Federal: built into bracket structure. Cantonal deductions vary significantly.',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Austria — Alleinverdienerabsetzbetrag
        // Source: https://www.bmf.gv.at
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['AT'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'basic_relief',
                'filing_status'   => 'default',
                'amount'          => 13308,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'EUR',
                'description'     => 'Tax-free threshold (Steuerfreigrenze)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Ireland — Tax credits (not deductions) — Standard Rate Cut-Off Point
        // Source: https://www.revenue.ie
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['IE'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 0,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'EUR',
                'description'     => 'Tax credit system: €1,875 personal + €1,875 PAYE credit',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Estonia — Non-taxable income
        // Source: https://www.emta.ee
        $eeData = ['2025' => 7848, '2026' => 7848];
        foreach ($eeData as $year => $amount) {
            $deductions[] = [
                'country_id'      => $countries['EE'] ?? null,
                'tax_year'        => (int) $year,
                'deduction_type'  => 'basic_relief',
                'filing_status'   => 'default',
                'amount'          => $amount,
                'is_percentage'   => false,
                'phase_out_start' => 14400,
                'phase_out_end'   => 25200,
                'currency_code'   => 'EUR',
                'description'     => 'Basic exemption (phases out between €14,400-€25,200)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Poland — Tax-free amount (kwota wolna od podatku)
        // Source: https://www.podatki.gov.pl
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['PL'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'basic_relief',
                'filing_status'   => 'default',
                'amount'          => 30000,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'PLN',
                'description'     => 'Tax-free amount (kwota wolna)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Czech Republic — Personal tax credit equivalent
        // Source: https://www.financnisprava.cz
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['CZ'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 0,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'CZK',
                'description'     => 'Tax credit system: CZK 30,840/year basic taxpayer credit',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Sweden — Basic allowance (grundavdrag)
        // Source: https://www.skatteverket.se
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['SE'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'basic_relief',
                'filing_status'   => 'default',
                'amount'          => 46200,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'SEK',
                'description'     => 'Grundavdrag (basic allowance, varies by income)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // ──────────────────────────────────────────────────────────────────────
        // ASIA-PACIFIC
        // ──────────────────────────────────────────────────────────────────────

        // Japan — Employment income deduction
        // Source: https://www.nta.go.jp
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['JP'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'employment_income',
                'filing_status'   => 'default',
                'amount'          => 550000,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'JPY',
                'description'     => 'Employment income deduction (min ¥550,000)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Japan — Basic exemption
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['JP'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'basic_relief',
                'filing_status'   => 'default',
                'amount'          => 480000,
                'is_percentage'   => false,
                'phase_out_start' => 24000000,
                'phase_out_end'   => 25000000,
                'currency_code'   => 'JPY',
                'description'     => 'Basic exemption (phases out above ¥24M)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Australia — Tax-free threshold
        // Source: https://www.ato.gov.au
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['AU'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 18200,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'AUD',
                'description'     => 'Tax-free threshold',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // South Korea — Standard wage/salary deduction
        // Source: https://www.nts.go.kr
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['KR'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'employment_income',
                'filing_status'   => 'default',
                'amount'          => 1500000,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'KRW',
                'description'     => 'Basic personal deduction (₩1.5M)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // India — Standard deduction
        // Source: https://www.incometax.gov.in
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['IN'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'standard',
                'filing_status'   => 'default',
                'amount'          => $year === 2025 ? 75000 : 75000,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'INR',
                'description'     => 'Standard deduction for salaried individuals (new tax regime)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Singapore — Earned income relief
        // Source: https://www.iras.gov.sg
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['SG'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 1000,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'SGD',
                'description'     => 'Earned income relief (S$1,000)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Thailand — Personal allowance
        // Source: https://www.rd.go.th
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['TH'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 60000,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'THB',
                'description'     => 'Personal allowance (฿60,000)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // New Zealand — No tax-free threshold (but low income rebate)
        // Source: https://www.ird.govt.nz
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['NZ'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 0,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'NZD',
                'description'     => 'No tax-free threshold; independent earner tax credit available',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // China — Standard deduction
        // Source: http://www.chinatax.gov.cn
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['CN'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'standard',
                'filing_status'   => 'default',
                'amount'          => 60000,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'CNY',
                'description'     => 'Basic standard deduction (¥5,000/month = ¥60,000/year)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Hong Kong — Basic allowance
        // Source: https://www.ird.gov.hk
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['HK'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 132000,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'HKD',
                'description'     => 'Basic allowance (HK$132,000)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Taiwan — Standard deduction
        // Source: https://www.ntbt.gov.tw
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['TW'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'standard',
                'filing_status'   => 'single',
                'amount'          => 131000,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'TWD',
                'description'     => 'Standard deduction for single filers (NT$131,000)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // ──────────────────────────────────────────────────────────────────────
        // SOUTH AMERICA
        // ──────────────────────────────────────────────────────────────────────

        // Brazil — Monthly tax-free threshold
        // Source: https://www.gov.br/receitafederal
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['BR'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => $year === 2026 ? 60000 : 28559.76,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'BRL',
                'description'     => $year === 2026 ? 'Annual exemption (R$5,000/month)' : 'Annual exemption (R$2,379.98/month)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Colombia — No standard deduction for individuals
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['CO'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'standard',
                'filing_status'   => 'default',
                'amount'          => 0,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'COP',
                'description'     => 'No standard deduction; itemized deductions available',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // ──────────────────────────────────────────────────────────────────────
        // MIDDLE EAST & AFRICA
        // ──────────────────────────────────────────────────────────────────────

        // Israel — Credit points system (per point ₪2,904 in 2025)
        // Source: https://www.gov.il/en/departments/taxes
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['IL'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 0,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'ILS',
                'description'     => 'Credit points system: 2.25 points × ₪2,904 = ₪6,534 tax credit',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // South Africa — Primary rebate (tax threshold)
        // Source: https://www.sars.gov.za
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['ZA'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => $year === 2025 ? 95750 : 97500,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'ZAR',
                'description'     => 'Tax-free threshold (below 65 years)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Pakistan — Tax-free threshold
        // Source: https://www.fbr.gov.pk
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['PK'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 600000,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'PKR',
                'description'     => 'Tax-free threshold (PKR 600,000/year)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // ──────────────────────────────────────────────────────────────────────
        // EASTERN EUROPE & FLAT-TAX COUNTRIES
        // ──────────────────────────────────────────────────────────────────────

        // Flat-tax countries with zero personal allowance built into brackets
        $flatTaxZeroDeduction = [
            'RO' => ['currency' => 'RON', 'name' => 'Romania'],
            'BG' => ['currency' => 'BGN', 'name' => 'Bulgaria'],
            'HU' => ['currency' => 'HUF', 'name' => 'Hungary'],
            'GE' => ['currency' => 'GEL', 'name' => 'Georgia'],
        ];
        foreach ($flatTaxZeroDeduction as $iso => $info) {
            foreach ([2025, 2026] as $year) {
                $deductions[] = [
                    'country_id'      => $countries[$iso] ?? null,
                    'tax_year'        => $year,
                    'deduction_type'  => 'standard',
                    'filing_status'   => 'default',
                    'amount'          => 0,
                    'is_percentage'   => false,
                    'phase_out_start' => null,
                    'phase_out_end'   => null,
                    'currency_code'   => $info['currency'],
                    'description'     => 'Flat tax with no separate personal allowance',
                    'is_active'       => true,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
        }

        // Croatia — Personal allowance
        // Source: https://www.porezna-uprava.hr
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['HR'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 5600,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'EUR',
                'description'     => 'Personal allowance (€560/month)',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // Greece — Tax-free threshold built into brackets (first €10,000 at 9%)
        foreach ([2025, 2026] as $year) {
            $deductions[] = [
                'country_id'      => $countries['GR'] ?? null,
                'tax_year'        => $year,
                'deduction_type'  => 'personal_allowance',
                'filing_status'   => 'default',
                'amount'          => 0,
                'is_percentage'   => false,
                'phase_out_start' => null,
                'phase_out_end'   => null,
                'currency_code'   => 'EUR',
                'description'     => 'Tax reduction code: €777 for income up to €10,000',
                'is_active'       => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        // ──────────────────────────────────────────────────────────────────────
        // ZERO-TAX COUNTRIES (no deduction needed)
        // ──────────────────────────────────────────────────────────────────────

        $zeroTaxCountries = ['AE', 'QA', 'SA', 'KW', 'BH', 'OM', 'MC', 'BS', 'KY', 'BM', 'VG', 'TC', 'AI', 'MV', 'VU', 'BN'];
        foreach ($zeroTaxCountries as $iso) {
            if (!isset($countries[$iso])) continue;
            foreach ([2025, 2026] as $year) {
                $deductions[] = [
                    'country_id'      => $countries[$iso] ?? null,
                    'tax_year'        => $year,
                    'deduction_type'  => 'standard',
                    'filing_status'   => 'default',
                    'amount'          => 0,
                    'is_percentage'   => false,
                    'phase_out_start' => null,
                    'phase_out_end'   => null,
                    'currency_code'   => 'USD',
                    'description'     => 'Zero personal income tax country — no deduction applicable',
                    'is_active'       => true,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
        }

        // Filter out any entries where country_id is null (country not in DB)
        $deductions = array_filter($deductions, fn($d) => $d['country_id'] !== null);

        // Insert in chunks
        foreach (array_chunk($deductions, 50) as $chunk) {
            DB::table('deductions')->insert($chunk);
        }
    }
}
