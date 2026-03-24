<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // ── Core / Universal ──────────────────────────────────────────────────
            [
                'key'         => 'income_tax',
                'name'        => 'Income Tax',
                'description' => 'Standard personal income tax applied to earned income. Universal across all jurisdictions.',
                'is_default'  => true,
                'is_active'   => true,
                'sort_order'  => 1,
            ],
            [
                'key'         => 'social_security',
                'name'        => 'Social Security / National Insurance',
                'description' => 'Social contributions covering healthcare, pension, and welfare. Known as NI in UK, Sécurité Sociale in France, Seguridad Social in Spain.',
                'is_default'  => true,
                'is_active'   => true,
                'sort_order'  => 2,
            ],
            [
                'key'         => 'corporate_tax',
                'name'        => 'Corporate Tax',
                'description' => 'Tax levied on company or entity profits. Relevant for freelancers operating through a limited company.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 3,
            ],
            [
                'key'         => 'capital_gains',
                'name'        => 'Capital Gains Tax',
                'description' => 'Tax on profit from disposal of assets such as stocks, property, or crypto. Rate and exemptions vary widely by country.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 4,
            ],
            [
                'key'         => 'dividend_tax',
                'name'        => 'Dividend Tax',
                'description' => 'Tax on dividend income received from shares. Separate box-system in Netherlands (Box 3), flat rate in Denmark and Germany.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 5,
            ],
            [
                'key'         => 'withholding_tax',
                'name'        => 'Withholding Tax',
                'description' => 'Tax deducted at source on payments such as dividends, royalties, and interest made to foreign residents.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 6,
            ],
 
            // ── Local / Regional ──────────────────────────────────────────────────
            [
                'key'         => 'municipal_tax',
                'name'        => 'Municipal / Local Tax',
                'description' => 'Additional tax levied by local municipalities or cantons. Common in Switzerland (Gemeinde), Sweden (kommunalskatt), Denmark, Finland.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 7,
            ],
            [
                'key'         => 'solidarity_surcharge',
                'name'        => 'Solidarity Surcharge',
                'description' => 'Additional levy on top of income tax. Germany Solidaritätszuschlag (5.5%), Hungary, and others.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 8,
            ],
            [
                'key'         => 'property_tax',
                'name'        => 'Property Tax',
                'description' => 'Annual tax on real estate or immovable property. Nearly universal; called IBI in Spain, Taxe Foncière in France, Council Tax in UK.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 9,
            ],
            [
                'key'         => 'wealth_tax',
                'name'        => 'Wealth Tax',
                'description' => 'Annual tax on net worth above a threshold. Active in Spain (Impuesto sobre el Patrimonio), Norway, Switzerland (Vermögenssteuer).',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 10,
            ],
 
            // ── Consumption ───────────────────────────────────────────────────────
            [
                'key'         => 'vat',
                'name'        => 'VAT / GST',
                'description' => 'Value Added Tax or Goods & Services Tax on consumption. Standard across EU, UK, Australia, Canada (GST/HST), India (GST).',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 11,
            ],
            [
                'key'         => 'sales_tax',
                'name'        => 'Sales Tax',
                'description' => 'State or provincial-level consumption tax. Primary model in the United States and some Canadian provinces.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 12,
            ],
 
            // ── Payroll / Employment ──────────────────────────────────────────────
            [
                'key'         => 'payroll_tax',
                'name'        => 'Payroll Tax',
                'description' => 'Employer-side labor taxes separate from employee contributions. Common in Australia (payroll tax), US (FUTA), France (taxe sur les salaires).',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 13,
            ],
            [
                'key'         => 'self_employment_tax',
                'name'        => 'Self-Employment Tax',
                'description' => 'Combined employer+employee social contributions for freelancers/sole traders. US SE Tax, Spain autónomo quota, France micro-entrepreneur.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 14,
            ],
            [
                'key'         => 'health_insurance',
                'name'        => 'Health Insurance Contribution',
                'description' => 'Mandatory health contribution where separate from general social security. Germany (Krankenversicherung ~14.6%), Switzerland (KVG premiums).',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 15,
            ],
            [
                'key'         => 'pension_contribution',
                'name'        => 'Pension Contribution',
                'description' => 'Mandatory state or private pension contributions. Netherlands AOW, Denmark ATP, UK auto-enrolment, Australia superannuation (11%).',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 16,
            ],
 
            // ── Transfer / Estate ─────────────────────────────────────────────────
            [
                'key'         => 'inheritance_tax',
                'name'        => 'Inheritance / Estate Tax',
                'description' => 'Tax on assets received upon death. UK (IHT 40%), Germany (Erbschaftsteuer), Japan (max 55%), Spain (Impuesto sobre Sucesiones).',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 17,
            ],
            [
                'key'         => 'gift_tax',
                'name'        => 'Gift Tax',
                'description' => 'Tax on transfer of assets as gifts during lifetime. US (annual exclusion $18k), Germany, Japan. Often linked to inheritance tax rules.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 18,
            ],
            [
                'key'         => 'stamp_duty',
                'name'        => 'Stamp Duty / Transfer Tax',
                'description' => 'Tax on legal documents and property transfers. UK SDLT, Australia stamp duty, Spain AJD/ITP, India stamp duty.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 19,
            ],
 
            // ── Special Nomad / Expat Regimes ─────────────────────────────────────
            [
                'key'         => 'territorial_tax',
                'name'        => 'Territorial Tax System',
                'description' => 'Only locally-sourced income is taxed; foreign income is exempt. Panama, Paraguay, Georgia, UAE, Costa Rica, Hong Kong, Singapore.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 20,
            ],
            [
                'key'         => 'remittance_basis',
                'name'        => 'Remittance Basis Tax',
                'description' => 'Foreign income taxed only when remitted (brought) into the country. UK non-domiciled regime, Malta, Ireland, Cyprus non-dom.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 21,
            ],
            [
                'key'         => 'flat_tax',
                'name'        => 'Flat / Lump-Sum Tax Regime',
                'description' => 'Fixed annual tax amount regardless of worldwide income. Italy €100k flat tax for new residents, Greece 7% pensioner regime, Swiss forfait.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 22,
            ],
            [
                'key'         => 'digital_nomad_regime',
                'name'        => 'Digital Nomad / Special Expat Regime',
                'description' => 'Country-specific incentive regimes for remote workers and expats. Portugal NHR/IFICI, Spain Beckham Law (24% flat), Greece 50% income discount, Italy impatriates regime.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 23,
            ],
            [
                'key'         => 'participation_exemption',
                'name'        => 'Participation Exemption',
                'description' => 'Exemption on dividends and capital gains from qualifying shareholdings. Netherlands deelnemingsvrijstelling, Luxembourg, Malta full imputation.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 24,
            ],
 
            // ── Asset-Specific ────────────────────────────────────────────────────
            [
                'key'         => 'crypto_tax',
                'name'        => 'Crypto / Digital Asset Tax',
                'description' => 'Tax treatment specific to cryptocurrency and digital assets. Germany (tax-free after 1yr hold), Portugal (exempt for non-professionals), Italy 26%, UAE 0%.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 25,
            ],
            [
                'key'         => 'financial_transaction_tax',
                'name'        => 'Financial Transaction Tax (FTT)',
                'description' => 'Tax on trading of stocks, bonds, or derivatives. France (0.3%), Italy (0.2%), Spain (0.2% on listed shares), Belgium TOB.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 26,
            ],
 
            // ── Exit / Mobility ───────────────────────────────────────────────────
            [
                'key'         => 'exit_tax',
                'name'        => 'Exit Tax',
                'description' => 'Tax triggered on unrealised gains when a taxpayer leaves a country. Germany, Netherlands (emigration levy), France, South Africa, Canada departure tax.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 27,
            ],
            [
                'key'         => 'deemed_domicile',
                'name'        => 'Deemed Domicile / Tax Residency Rule',
                'description' => 'Rule that treats a long-term resident as domiciled for tax purposes even if domicile of origin is elsewhere. UK 15/20 year rule, Ireland.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 28,
            ],
 
            // ── Business / Indirect ───────────────────────────────────────────────
            [
                'key'         => 'turnover_tax',
                'name'        => 'Turnover / Presumptive Tax',
                'description' => 'Tax based on gross revenue rather than profit. Common for micro-businesses and freelancers in France (micro-BIC/BNC), Portugal (regime simplificado), Romania.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 29,
            ],
            [
                'key'         => 'minimum_tax',
                'name'        => 'Minimum / Alternative Minimum Tax',
                'description' => 'Floor tax ensuring a minimum liability regardless of deductions. US AMT, Canada AMT, OECD Pillar Two global minimum 15% for corporates.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 30,
            ],
            [
                'key'         => 'trade_tax',
                'name'        => 'Trade / Business Tax',
                'description' => 'Local business activity tax separate from corporate income tax. Germany Gewerbesteuer (7–17% effective), Austria Kommunalsteuer.',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 31,
            ],
        ];

        DB::table('tax_types')->insert($types);
    }
}
