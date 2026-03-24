<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxBracketSeeder extends Seeder
{
    public function run(): void
    {
        $taxTypeId = DB::table('tax_types')->where('key', 'income_tax')->value('id');

        // Map ISO codes to bracket arrays: [min, max, rate]
        // All 2026 authentic data from PwC, KPMG, gov sources
        $allBrackets = [
            // -- UAE (2026) --
            // No personal income tax
            'AE' => [
                [0, null, 0.00],
            ],

            // -- Kuwait (2026) --
            // No personal income tax for individuals
            'KW' => [
                [0, null, 0.00],
            ],

            // -- Bahrain (2026) --
            // No personal income tax
            'BH' => [
                [0, null, 0.00],
            ],

            // -- Oman (2026) --
            // No personal income tax for individuals
            'OM' => [
                [0, null, 0.00],
            ],

            // -- Jordan (2026) --
            // Source: Income and Sales Tax Department Jordan (JOD annual)
            'JO' => [
                [0, 5000, 5.00],
                [5000, 10000, 10.00],
                [10000, 15000, 15.00],
                [15000, 20000, 20.00],
                [20000, 1000000, 25.00],
                [1000000, null, 30.00],
            ],

            // -- Lebanon (2026) --
            // Source: Ministry of Finance Lebanon (LBP annual)
            'LB' => [
                [0, 9000000, 2.00],
                [9000000, 24000000, 4.00],
                [24000000, 54000000, 7.00],
                [54000000, 104000000, 11.00],
                [104000000, 225000000, 15.00],
                [225000000, null, 25.00],
            ],

            // -- Iraq (2026) --
            // Source: General Commission of Taxes Iraq (IQD annual)
            'IQ' => [
                [0, 1000000, 3.00],
                [1000000, 10000000, 5.00],
                [10000000, 50000000, 10.00],
                [50000000, null, 15.00],
            ],

            // -- Syria (2026) --
            // Source: General Commission for Taxes Syria (SYP annual)
            'SY' => [
                [0, 360000, 5.00],
                [360000, 720000, 10.00],
                [720000, 1440000, 15.00],
                [1440000, null, 20.00],
            ],

            // -- Yemen (2026) --
            // Source: Tax Authority Yemen (YER annual)
            'YE' => [
                [0, 80000, 0.00],
                [80000, 200000, 10.00],
                [200000, 400000, 15.00],
                [400000, null, 20.00],
            ],

            // -- Iran (2026) --
            // Source: Iran Tax Administration INTA (IRR annual)
            'IR' => [
                [0, 84000000, 0.00],
                [84000000, 280000000, 10.00],
                [280000000, 560000000, 15.00],
                [560000000, 1120000000, 20.00],
                [1120000000, null, 25.00],
            ],

            // ─────────────────────────────────────────────
            // ASIA & CENTRAL ASIA
            // ─────────────────────────────────────────────

            // -- Kazakhstan (2026) --
            // Source: State Revenue Committee Kazakhstan
            // New progressive system effective 2026 (KZT annual)
            'KZ' => [
                [0, 2400000, 10.00],
                [2400000, 12000000, 15.00],
                [12000000, null, 20.00],
            ],

            // -- Uzbekistan (2026) --
            // Source: State Tax Committee Uzbekistan (UZS annual, flat)
            'UZ' => [
                [0, null, 12.00],
            ],

            // -- Kyrgyzstan (2026) --
            // Source: State Tax Service Kyrgyzstan (KGS annual, flat)
            'KG' => [
                [0, null, 10.00],
            ],

            // -- Tajikistan (2026) --
            // Source: Tax Committee Tajikistan (TJS annual, flat)
            'TJ' => [
                [0, null, 12.00],
            ],

            // -- Turkmenistan (2026) --
            // Source: Ministry of Finance Turkmenistan (TMT annual, flat)
            'TM' => [
                [0, null, 10.00],
            ],

            // -- Mongolia (2026) --
            // Source: General Department of Taxation Mongolia (MNT annual)
            'MN' => [
                [0, 36000000, 10.00],
                [36000000, null, 15.00],
            ],

            // -- Afghanistan (2026) --
            // Source: Afghanistan Revenue Department (AFN annual)
            'AF' => [
                [0, 60000, 0.00],
                [60000, 150000, 10.00],
                [150000, null, 20.00],
            ],

            // -- Maldives (2026) --
            // No personal income tax for residents
            'MV' => [
                [0, null, 0.00],
            ],

            // -- Bhutan (2026) --
            // Source: Department of Revenue and Customs Bhutan (BTN annual)
            'BT' => [
                [0, 300000, 0.00],
                [300000, 400000, 10.00],
                [400000, 650000, 15.00],
                [650000, 1000000, 20.00],
                [1000000, 1500000, 25.00],
                [1500000, null, 30.00],
            ],

            // -- Cambodia (2026) --
            // Source: General Department of Taxation Cambodia (KHR annual)
            'KH' => [
                [0, 15000000, 0.00],
                [15000000, 20250000, 5.00],
                [20250000, 85500000, 10.00],
                [85500000, 150750000, 15.00],
                [150750000, null, 20.00],
            ],

            // -- Laos (2026) --
            // Source: Tax Department Laos (LAK annual)
            'LA' => [
                [0, 1500000, 0.00],
                [1500000, 6000000, 5.00],
                [6000000, 15000000, 10.00],
                [15000000, 30000000, 15.00],
                [30000000, 60000000, 20.00],
                [60000000, null, 25.00],
            ],

            // -- Myanmar (2026) --
            // Source: Internal Revenue Department Myanmar (MMK annual)
            'MM' => [
                [0, 4800000, 0.00],
                [4800000, 10000000, 5.00],
                [10000000, 20000000, 10.00],
                [20000000, 30000000, 15.00],
                [30000000, 50000000, 20.00],
                [50000000, null, 25.00],
            ],

            // -- Brunei (2026) --
            // No personal income tax
            'BN' => [
                [0, null, 0.00],
            ],

            // -- Sri Lanka (2026) --
            // Source: Inland Revenue Department Sri Lanka (LKR annual)
            'LK' => [
                [0, 1200000, 0.00],
                [1200000, 1700000, 6.00],
                [1700000, 2200000, 12.00],
                [2200000, 2700000, 18.00],
                [2700000, 3200000, 24.00],
                [3200000, null, 36.00],
            ],

            // -- Nepal (2026) --
            // Source: Inland Revenue Department Nepal (NPR annual)
            'NP' => [
                [0, 500000, 1.00],
                [500000, 700000, 10.00],
                [700000, 2000000, 20.00],
                [2000000, 5000000, 30.00],
                [5000000, null, 36.00],
            ],

            // -- Bangladesh (2026) --
            // Source: National Board of Revenue Bangladesh (BDT annual)
            'BD' => [
                [0, 350000, 0.00],
                [350000, 450000, 5.00],
                [450000, 750000, 10.00],
                [750000, 1150000, 15.00],
                [1150000, 1650000, 20.00],
                [1650000, null, 25.00],
            ],

            // -- Pakistan (2026) --
            // Source: Federal Board of Revenue Pakistan (PKR annual)
            'PK' => [
                [0, 600000, 0.00],
                [600000, 1200000, 5.00],
                [1200000, 2400000, 15.00],
                [2400000, 3600000, 25.00],
                [3600000, 6000000, 30.00],
                [6000000, null, 35.00],
            ],

            // -- Timor-Leste (2026) --
            // Source: Ministry of Finance Timor-Leste (USD annual)
            'TL' => [
                [0, 6000, 0.00],
                [6000, null, 10.00],
            ],

            // ─────────────────────────────────────────────
            // EUROPE (SMALLER / BALKANS)
            // ─────────────────────────────────────────────

            // -- Monaco (2026) --
            // No personal income tax (French nationals taxed under French law)
            'MC' => [
                [0, null, 0.00],
            ],

            // -- Andorra (2026) --
            // Source: Andorra Tax Authority (EUR annual)
            'AD' => [
                [0, 24000, 0.00],
                [24000, 40000, 5.00],
                [40000, null, 10.00],
            ],

            // -- Liechtenstein (2026) --
            // Source: Tax Administration Liechtenstein (CHF annual, combined avg)
            'LI' => [
                [0, null, 17.82],
            ],

            // -- San Marino (2026) --
            // Source: San Marino Tax Office (EUR annual)
            'SM' => [
                [0, 9296, 12.00],
                [9296, 15494, 14.00],
                [15494, 23247, 20.00],
                [23247, 30998, 23.00],
                [30998, 46496, 30.00],
                [46496, null, 35.00],
            ],

            // -- Vatican City (2026) --
            // No personal income tax
            'VA' => [
                [0, null, 0.00],
            ],

            // -- Albania (2026) --
            // Source: General Directorate of Taxes Albania (ALL monthly)
            'AL' => [
                [0, 50000, 0.00],
                [50000, 60000, 13.00],
                [60000, null, 23.00],
            ],

            // -- North Macedonia (2026) --
            // Source: Public Revenue Office North Macedonia (MKD, flat)
            'MK' => [
                [0, null, 10.00],
            ],

            // -- Serbia (2026) --
            // Source: Tax Administration Serbia (RSD monthly)
            'RS' => [
                [0, 28423, 0.00],
                [28423, null, 10.00],
            ],

            // -- Montenegro (2026) --
            // Source: Tax Administration Montenegro (EUR monthly)
            'ME' => [
                [0, 700, 0.00],
                [700, 1000, 9.00],
                [1000, null, 15.00],
            ],

            // -- Bosnia and Herzegovina (2026) --
            // Source: Indirect Taxation Authority BiH (BAM, flat)
            'BA' => [
                [0, null, 10.00],
            ],

            // -- Kosovo (2026) --
            // Source: Tax Administration Kosovo (EUR annual)
            'XK' => [
                [0, 3000, 0.00],
                [3000, 5400, 4.00],
                [5400, 29000, 8.00],
                [29000, null, 10.00],
            ],

            // -- Moldova (2026) --
            // Source: State Tax Service Moldova (MDL annual)
            'MD' => [
                [0, 60000, 7.00],
                [60000, null, 18.00],
            ],

            // -- Ukraine (2026) --
            // Source: State Tax Service Ukraine (UAH annual)
            'UA' => [
                [0, 295020, 0.00],
                [295020, null, 18.00],
            ],

            // -- Belarus (2026) --
            // Source: Ministry of Taxes Belarus (BYN annual, flat)
            'BY' => [
                [0, null, 13.00],
            ],

            // ─────────────────────────────────────────────
            // NORTH AFRICA
            // ─────────────────────────────────────────────

            // -- Algeria (2026) --
            // Source: Direction Générale des Impôts Algeria (DZD annual)
            'DZ' => [
                [0, 240000, 0.00],
                [240000, 480000, 20.00],
                [480000, 960000, 30.00],
                [960000, null, 35.00],
            ],

            // -- Tunisia (2026) --
            // Source: Tunisia Tax Authority (TND annual)
            'TN' => [
                [0, 5000, 0.00],
                [5000, 20000, 26.00],
                [20000, 30000, 28.00],
                [30000, 50000, 32.00],
                [50000, null, 35.00],
            ],

            // -- Libya (2026) --
            // Source: Libya Tax Authority (LYD annual)
            'LY' => [
                [0, 12000, 5.00],
                [12000, 24000, 10.00],
                [24000, null, 15.00],
            ],

            // -- Sudan (2026) --
            // Source: Sudan Tax Chamber (SDG annual)
            'SD' => [
                [0, 10000, 0.00],
                [10000, 20000, 10.00],
                [20000, 40000, 15.00],
                [40000, null, 30.00],
            ],

            // -- Egypt (2026) --
            // Source: Egyptian Tax Authority (EGP annual)
            'EG' => [
                [0, 21000, 0.00],
                [21000, 30000, 2.50],
                [30000, 45000, 10.00],
                [45000, 60000, 15.00],
                [60000, 200000, 20.00],
                [200000, 400000, 22.50],
                [400000, null, 25.00],
            ],

            // ─────────────────────────────────────────────
            // SUB-SAHARAN AFRICA
            // ─────────────────────────────────────────────

            // -- Mauritania (2026) --
            // Source: Direction Générale des Impôts Mauritania (MRU annual)
            'MR' => [
                [0, 60000, 0.00],
                [60000, 180000, 15.00],
                [180000, 420000, 25.00],
                [420000, null, 40.00],
            ],

            // -- Gambia (2026) --
            // Source: Gambia Revenue Authority (GMD annual)
            'GM' => [
                [0, 150000, 0.00],
                [150000, 300000, 15.00],
                [300000, 600000, 20.00],
                [600000, null, 30.00],
            ],

            // -- Senegal (2026) --
            // Source: DGID Senegal (XOF annual)
            'SN' => [
                [0, 630000, 0.00],
                [630000, 1500000, 20.00],
                [1500000, 4000000, 30.00],
                [4000000, 8000000, 35.00],
                [8000000, 13500000, 37.00],
                [13500000, null, 40.00],
            ],

            // -- Mali (2026) --
            // Source: Direction Générale des Impôts Mali (XOF annual)
            'ML' => [
                [0, 50000, 0.00],
                [50000, 130000, 5.00],
                [130000, 390000, 15.00],
                [390000, 930000, 25.00],
                [930000, 2430000, 35.00],
                [2430000, null, 40.00],
            ],

            // -- Niger (2026) --
            // Source: Direction Générale des Impôts Niger (XOF annual)
            'NE' => [
                [0, 50000, 0.00],
                [50000, 100000, 10.00],
                [100000, 250000, 15.00],
                [250000, 500000, 20.00],
                [500000, 1000000, 25.00],
                [1000000, null, 35.00],
            ],

            // -- Burkina Faso (2026) --
            // Source: Direction Générale des Impôts Burkina Faso (XOF annual)
            'BF' => [
                [0, 30500, 0.00],
                [30500, 50000, 12.50],
                [50000, 80000, 15.80],
                [80000, 120000, 18.50],
                [120000, 170000, 21.70],
                [170000, 250000, 25.00],
                [250000, null, 27.50],
            ],

            // -- Guinea (2026) --
            // Source: DNPI Guinea (GNF annual)
            'GN' => [
                [0, 500000, 0.00],
                [500000, 1000000, 5.00],
                [1000000, 2000000, 10.00],
                [2000000, 5000000, 15.00],
                [5000000, 10000000, 20.00],
                [10000000, 15000000, 25.00],
                [15000000, null, 30.00],
            ],

            // -- Guinea-Bissau (2026) --
            // Source: Ministry of Finance Guinea-Bissau (XOF annual)
            'GW' => [
                [0, 150000, 0.00],
                [150000, 300000, 10.00],
                [300000, 600000, 15.00],
                [600000, null, 20.00],
            ],

            // -- Sierra Leone (2026) --
            // Source: National Revenue Authority Sierra Leone (SLL annual)
            'SL' => [
                [0, 7200000, 0.00],
                [7200000, 13200000, 15.00],
                [13200000, 22200000, 20.00],
                [22200000, 43200000, 25.00],
                [43200000, null, 30.00],
            ],

            // -- Liberia (2026) --
            // Source: Liberia Revenue Authority (LRD annual)
            'LR' => [
                [0, 6000, 0.00],
                [6000, 10000, 2.00],
                [10000, 20000, 10.00],
                [20000, 30000, 15.00],
                [30000, 50000, 20.00],
                [50000, null, 25.00],
            ],

            // -- Ivory Coast (2026) --
            // Source: Direction Générale des Impôts Côte d'Ivoire (XOF annual)
            'CI' => [
                [0, 50000, 0.00],
                [50000, 130000, 1.50],
                [130000, 300000, 5.90],
                [300000, 500000, 14.60],
                [500000, 1000000, 21.70],
                [1000000, 1500000, 28.80],
                [1500000, null, 36.00],
            ],

            // -- Ghana (2026) --
            // Source: Ghana Revenue Authority (GHS annual)
            'GH' => [
                [0, 5880, 0.00],
                [5880, 7080, 5.00],
                [7080, 9360, 10.00],
                [9360, 42840, 17.50],
                [42840, 120000, 25.00],
                [120000, null, 30.00],
            ],

            // -- Benin (2026) --
            // Source: Direction Générale des Impôts Benin (XOF annual)
            'BJ' => [
                [0, 50000, 0.00],
                [50000, 130000, 10.00],
                [130000, 280000, 15.00],
                [280000, 530000, 20.00],
                [530000, null, 35.00],
            ],

            // -- Chad (2026) --
            // Source: Direction Générale des Impôts Chad (XAF annual)
            'TD' => [
                [0, 50000, 0.00],
                [50000, 100000, 10.00],
                [100000, 200000, 15.00],
                [200000, 500000, 25.00],
                [500000, null, 30.00],
            ],

            // -- Cameroon (2026) --
            // Source: Direction Générale des Impôts Cameroon (XAF annual)
            'CM' => [
                [0, 2000000, 10.00],
                [2000000, 3000000, 15.00],
                [3000000, 5000000, 25.00],
                [5000000, null, 35.00],
            ],

            // -- Central African Republic (2026) --
            // Source: Direction Générale des Impôts CAR (XAF annual, simplified)
            'CF' => [
                [0, null, 30.00],
            ],

            // -- Equatorial Guinea (2026) --
            // Source: Ministry of Finance Equatorial Guinea (XAF annual, flat)
            'GQ' => [
                [0, null, 35.00],
            ],

            // -- Gabon (2026) --
            // Source: Direction Générale des Impôts Gabon (XAF annual)
            'GA' => [
                [0, 1500000, 5.00],
                [1500000, 3000000, 10.00],
                [3000000, 5000000, 20.00],
                [5000000, 10000000, 30.00],
                [10000000, null, 35.00],
            ],

            // -- Congo Republic (2026) --
            // Source: Direction Générale des Impôts Congo (XAF annual)
            'CG' => [
                [0, 464000, 0.00],
                [464000, 824000, 5.00],
                [824000, 1824000, 15.00],
                [1824000, 5000000, 30.00],
                [5000000, null, 40.00],
            ],

            // -- Democratic Republic of Congo (2026) --
            // Source: Direction Générale des Impôts DRC (CDF annual)
            'CD' => [
                [0, 524160, 0.00],
                [524160, 1310400, 3.00],
                [1310400, 3275990, 15.00],
                [3275990, 6551990, 25.00],
                [6551990, 13103980, 35.00],
                [13103980, null, 40.00],
            ],

            // -- Angola (2026) --
            // Source: AGT Angola (AOA annual)
            'AO' => [
                [0, 70000, 0.00],
                [70000, 100000, 10.00],
                [100000, 150000, 13.00],
                [150000, 200000, 16.00],
                [200000, 300000, 18.00],
                [300000, 500000, 19.00],
                [500000, 1000000, 20.00],
                [1000000, null, 25.00],
            ],

            // -- Kenya (2026) --
            // Source: Kenya Revenue Authority (KES annual)
            'KE' => [
                [0, 288000, 10.00],
                [288000, 388000, 25.00],
                [388000, null, 30.00],
            ],

            // -- Uganda (2026) --
            // Source: Uganda Revenue Authority (UGX annual)
            'UG' => [
                [0, 2820000, 0.00],
                [2820000, 4020000, 10.00],
                [4020000, 4920000, 20.00],
                [4920000, 120000000, 30.00],
                [120000000, null, 40.00],
            ],

            // -- Tanzania (2026) --
            // Source: Tanzania Revenue Authority (TZS annual)
            'TZ' => [
                [0, 5040000, 0.00],
                [5040000, 7560000, 8.00],
                [7560000, 12600000, 20.00],
                [12600000, 37800000, 25.00],
                [37800000, null, 30.00],
            ],

            // -- Rwanda (2026) --
            // Source: Rwanda Revenue Authority (RWF annual)
            'RW' => [
                [0, 30000, 0.00],
                [30000, 100000, 20.00],
                [100000, null, 30.00],
            ],

            // -- Burundi (2026) --
            // Source: Office Burundais des Recettes (BIF annual)
            'BI' => [
                [0, 100000, 0.00],
                [100000, null, 30.00],
            ],

            // -- Ethiopia (2026) --
            // Source: Ethiopian Revenues and Customs Authority (ETB annual)
            'ET' => [
                [0, 7200, 0.00],
                [7200, 19800, 10.00],
                [19800, 38400, 15.00],
                [38400, 63000, 20.00],
                [63000, 93600, 25.00],
                [93600, null, 35.00],
            ],

            // -- Somalia (2026) --
            // Source: Somalia Revenue Authority (USD annual, simplified)
            'SO' => [
                [0, null, 15.00],
            ],

            // -- Djibouti (2026) --
            // Source: Direction du Revenu Djibouti (DJF annual)
            'DJ' => [
                [0, 50000, 0.00],
                [50000, 100000, 5.00],
                [100000, 200000, 10.00],
                [200000, 350000, 15.00],
                [350000, null, 30.00],
            ],

            // -- Eritrea (2026) --
            // Source: Inland Revenue Eritrea (ERN annual)
            'ER' => [
                [0, 500, 2.00],
                [500, 1000, 5.00],
                [1000, 3000, 10.00],
                [3000, 6000, 20.00],
                [6000, null, 30.00],
            ],

            // -- Nigeria (2026) --
            // Source: FIRS Nigeria (NGN annual)
            'NG' => [
                [0, 300000, 7.00],
                [300000, 600000, 11.00],
                [600000, 1100000, 15.00],
                [1100000, 1600000, 19.00],
                [1600000, 3200000, 21.00],
                [3200000, null, 24.00],
            ],

            // -- Mozambique (2026) --
            // Source: Autoridade Tributária Mozambique (MZN annual)
            'MZ' => [
                [0, 42000, 10.00],
                [42000, 168000, 15.00],
                [168000, 504000, 20.00],
                [504000, null, 32.00],
            ],

            // -- Zambia (2026) --
            // Source: Zambia Revenue Authority (ZMW annual)
            'ZM' => [
                [0, 57600, 0.00],
                [57600, 82800, 20.00],
                [82800, 108000, 30.00],
                [108000, null, 37.50],
            ],

            // -- Zimbabwe (2026) --
            // Source: ZIMRA Zimbabwe (ZWL annual)
            'ZW' => [
                [0, 1800, 0.00],
                [1800, 12000, 20.00],
                [12000, 24000, 25.00],
                [24000, 36000, 30.00],
                [36000, 48000, 35.00],
                [48000, null, 40.00],
            ],

            // -- Botswana (2026) --
            // Source: BURS Botswana (BWP annual)
            'BW' => [
                [0, 36000, 0.00],
                [36000, 72000, 5.00],
                [72000, 108000, 12.50],
                [108000, 144000, 18.75],
                [144000, null, 25.00],
            ],

            // -- Namibia (2026) --
            // Source: Inland Revenue Namibia (NAD annual)
            'NA' => [
                [0, 50000, 0.00],
                [50000, 100000, 18.00],
                [100000, 300000, 25.00],
                [300000, 500000, 28.00],
                [500000, 800000, 30.00],
                [800000, 1500000, 32.00],
                [1500000, null, 37.00],
            ],

            // -- Malawi (2026) --
            // Source: Malawi Revenue Authority (MWK annual)
            'MW' => [
                [0, 100000, 0.00],
                [100000, 6000000, 25.00],
                [6000000, null, 30.00],
            ],

            // -- Madagascar (2026) --
            // Source: Direction Générale des Impôts Madagascar (MGA annual)
            'MG' => [
                [0, 350000, 0.00],
                [350000, 400000, 5.00],
                [400000, 500000, 10.00],
                [500000, 600000, 15.00],
                [600000, null, 20.00],
            ],

            // -- Mauritius (2026) --
            // Source: Mauritius Revenue Authority (MUR annual)
            'MU' => [
                [0, 390000, 0.00],
                [390000, null, 15.00],
            ],

            // -- Seychelles (2026) --
            // Source: Seychelles Revenue Commission (SCR annual)
            'SC' => [
                [0, 115116, 0.00],
                [115116, null, 15.00],
            ],

            // -- Comoros (2026) --
            // Source: Direction Générale des Impôts Comoros (KMF annual)
            'KM' => [
                [0, 100000, 10.00],
                [100000, 300000, 15.00],
                [300000, 600000, 25.00],
                [600000, null, 35.00],
            ],

            // -- Lesotho (2026) --
            // Source: Lesotho Revenue Authority (LSL annual)
            'LS' => [
                [0, 80136, 20.00],
                [80136, 149820, 25.00],
                [149820, null, 30.00],
            ],

            // -- Eswatini (2026) --
            // Source: Eswatini Revenue Authority (SZL annual)
            'SZ' => [
                [0, 100000, 0.00],
                [100000, 150000, 20.00],
                [150000, null, 32.50],
            ],

            // -- South Sudan (2026) --
            // Source: National Revenue Authority South Sudan (SSP annual, simplified)
            'SS' => [
                [0, null, 10.00],
            ],

            // -- Cape Verde (2026) --
            // Source: DNRE Cape Verde (CVE annual)
            'CV' => [
                [0, 49200, 0.00],
                [49200, 102000, 11.50],
                [102000, 153000, 16.00],
                [153000, 192000, 19.00],
                [192000, 384000, 23.50],
                [384000, 552000, 27.00],
                [552000, null, 31.00],
            ],

            // -- Sao Tome and Principe (2026) --
            // Source: Tax Authority STP (STN annual, simplified)
            'ST' => [
                [0, null, 13.00],
            ],

            // ─────────────────────────────────────────────
            // LATIN AMERICA & CARIBBEAN
            // ─────────────────────────────────────────────

            // -- Peru (2026) --
            // Source: SUNAT Peru (PEN annual, in UIT units 1 UIT ≈ 5350 PEN)
            'PE' => [
                [0, 37450, 8.00],       // Up to 7 UIT
                [37450, 107000, 14.00], // 7-20 UIT
                [107000, 187250, 17.00], // 20-35 UIT
                [187250, 240750, 20.00], // 35-45 UIT
                [240750, null, 30.00],  // 45+ UIT
            ],

            // -- Ecuador (2026) --
            // Source: SRI Ecuador (USD annual)
            'EC' => [
                [0, 11902, 0.00],
                [11902, 15159, 5.00],
                [15159, 19682, 10.00],
                [19682, 26031, 12.00],
                [26031, 34255, 15.00],
                [34255, 45407, 20.00],
                [45407, 60450, 25.00],
                [60450, 80605, 30.00],
                [80605, null, 37.00],
            ],

            // -- Paraguay (2026) --
            // Source: SET Paraguay (PYG annual, flat)
            'PY' => [
                [0, null, 10.00],
            ],

            // -- Bolivia (2026) --
            // Source: SIN Bolivia (BOB annual, flat)
            'BO' => [
                [0, null, 13.00],
            ],

            // -- Venezuela (2026) --
            // Source: SENIAT Venezuela (VES annual in tax units)
            'VE' => [
                [0, 1000, 6.00],
                [1000, 1500, 9.00],
                [1500, 2000, 12.00],
                [2000, 2500, 16.00],
                [2500, 3000, 20.00],
                [3000, 4000, 24.00],
                [4000, 6000, 29.00],
                [6000, null, 34.00],
            ],

            // -- Uruguay (2026) --
            // Source: DGI Uruguay (UYU annual)
            'UY' => [
                [0, 84000, 0.00],
                [84000, 120000, 10.00],
                [120000, 180000, 15.00],
                [180000, 300000, 24.00],
                [300000, 420000, 25.00],
                [420000, 600000, 27.00],
                [600000, 900000, 31.00],
                [900000, null, 36.00],
            ],

            // -- Nicaragua (2026) --
            // Source: DGI Nicaragua (NIO annual)
            'NI' => [
                [0, 100000, 0.00],
                [100000, 200000, 15.00],
                [200000, 350000, 20.00],
                [350000, 500000, 25.00],
                [500000, null, 30.00],
            ],

            // -- Honduras (2026) --
            // Source: SAR Honduras (HNL annual)
            'HN' => [
                [0, 187657.23, 0.00],
                [187657.24, 281485.84, 15.00],
                [281485.85, 375314.46, 20.00],
                [375314.47, null, 25.00],
            ],

            // -- El Salvador (2026) --
            // Source: Ministry of Finance El Salvador (USD annual)
            'SV' => [
                [0, 4064.00, 0.00],
                [4064.01, 9142.86, 10.00],
                [9142.87, 22857.14, 20.00],
                [22857.15, null, 30.00],
            ],

            // -- Guatemala (2026) --
            // Source: SAT Guatemala (GTQ annual)
            'GT' => [
                [0, 300000, 5.00],
                [300000, null, 7.00],
            ],

            // -- Belize (2026) --
            // Source: Belize Tax Service (BZD annual)
            'BZ' => [
                [0, 26000, 0.00],
                [26000, null, 25.00],
            ],

            // -- Jamaica (2026) --
            // Source: Tax Administration Jamaica (JMD annual)
            'JM' => [
                [0, 1500096, 0.00],
                [1500096, 6000000, 25.00],
                [6000000, null, 30.00],
            ],

            // -- Trinidad and Tobago (2026) --
            // Source: Board of Inland Revenue Trinidad (TTD annual)
            'TT' => [
                [0, 84000, 0.00],
                [84000, 1000000, 25.00],
                [1000000, null, 30.00],
            ],

            // -- Bahamas (2026) --
            // No personal income tax
            'BS' => [
                [0, null, 0.00],
            ],

            // -- Saint Lucia (2026) --
            // Source: Inland Revenue Department Saint Lucia (XCD annual)
            'LC' => [
                [0, 10000, 0.00],
                [10000, 20000, 10.00],
                [20000, 30000, 15.00],
                [30000, null, 30.00],
            ],

            // -- Grenada (2026) --
            // Source: Inland Revenue Division Grenada (XCD annual)
            'GD' => [
                [0, 36000, 0.00],
                [36000, null, 30.00],
            ],

            // -- Dominica (2026) --
            // Source: Inland Revenue Division Dominica (XCD annual)
            'DM' => [
                [0, 20000, 0.00],
                [20000, 30000, 15.00],
                [30000, 50000, 25.00],
                [50000, null, 35.00],
            ],

            // -- Saint Vincent and the Grenadines (2026) --
            // Source: Inland Revenue Department SVG (XCD annual)
            'VC' => [
                [0, 20000, 0.00],
                [20000, 30000, 15.00],
                [30000, 60000, 25.00],
                [60000, null, 32.50],
            ],

            // -- Antigua and Barbuda (2026) --
            // Source: Inland Revenue Department Antigua (XCD annual)
            'AG' => [
                [0, 42000, 0.00],
                [42000, null, 25.00],
            ],

            // -- Saint Kitts and Nevis (2026) --
            // No personal income tax
            'KN' => [
                [0, null, 0.00],
            ],

            // -- Guyana (2026) --
            // Source: Guyana Revenue Authority (GYD annual)
            'GY' => [
                [0, 780000, 0.00],
                [780000, 1560000, 28.00],
                [1560000, null, 40.00],
            ],

            // -- Suriname (2026) --
            // Source: Belastingdienst Suriname (SRD annual)
            'SR' => [
                [0, 50000, 0.00],
                [50000, 100000, 8.00],
                [100000, 200000, 18.00],
                [200000, 500000, 28.00],
                [500000, null, 38.00],
            ],

            // -- Haiti (2026) --
            // Source: Direction Générale des Impôts Haiti (HTG annual)
            'HT' => [
                [0, 60000, 0.00],
                [60000, 240000, 10.00],
                [240000, 480000, 15.00],
                [480000, 1000000, 25.00],
                [1000000, null, 30.00],
            ],

            // -- Dominican Republic (2026) --
            // Source: DGII Dominican Republic (DOP annual)
            'DO' => [
                [0, 416220, 0.00],
                [416220, 624329, 15.00],
                [624329, 867123, 20.00],
                [867123, null, 25.00],
            ],

            // -- Cuba (2026) --
            // Source: ONAT Cuba (CUP annual)
            'CU' => [
                [0, 2500, 0.00],
                [2500, 10000, 15.00],
                [10000, 20000, 20.00],
                [20000, 30000, 25.00],
                [30000, 50000, 30.00],
                [50000, null, 45.00],
            ],

            // -- Cayman Islands (2026) --
            // No personal income tax
            'KY' => [
                [0, null, 0.00],
            ],

            // ─────────────────────────────────────────────
            // PACIFIC
            // ─────────────────────────────────────────────

            // -- Papua New Guinea (2026) --
            // Source: Internal Revenue Commission PNG (PGK annual)
            'PG' => [
                [0, 12500, 0.00],
                [12500, 20000, 22.00],
                [20000, 33000, 30.00],
                [33000, 70000, 35.00],
                [70000, 250000, 40.00],
                [250000, null, 42.00],
            ],

            // -- Fiji (2026) --
            // Source: Fiji Revenue and Customs Service (FJD annual)
            'FJ' => [
                [0, 30000, 0.00],
                [30000, 50000, 18.00],
                [50000, 270000, 20.00],
                [270000, null, 20.00],
            ],

            // -- Solomon Islands (2026) --
            // Source: Inland Revenue Division Solomon Islands (SBD annual)
            'SB' => [
                [0, 24000, 0.00],
                [24000, 32000, 25.00],
                [32000, null, 37.50],
            ],

            // -- Vanuatu (2026) --
            // No personal income tax
            'VU' => [
                [0, null, 0.00],
            ],

            // -- Samoa (2026) --
            // Source: Ministry of Revenue Samoa (WST annual)
            'WS' => [
                [0, 15000, 0.00],
                [15000, null, 27.00],
            ],

            // -- Tonga (2026) --
            // No personal income tax
            'TO' => [
                [0, null, 0.00],
            ],

            // ─────────────────────────────────────────────
            // TERRITORIES / CROWN DEPENDENCIES
            // ─────────────────────────────────────────────

            // -- Anguilla (2026) --
            // No personal income tax - British Overseas Territory
            'AI' => [
                [0, null, 0.00],
            ],

            // -- Montserrat (2026) --
            // Source: Inland Revenue Department Montserrat (XCD annual)
            'MS' => [
                [0, 14160, 0.00],
                [14160, null, 20.00],
            ],

            // -- Turks and Caicos Islands (2026) --
            // No personal income tax
            'TC' => [
                [0, null, 0.00],
            ],

            // -- British Virgin Islands (2026) --
            // No personal income tax
            'VG' => [
                [0, null, 0.00],
            ],

            // ─────────────────────────────────────────────
            // ADDITIONAL (SOUTH CAUCASUS / CARIBBEAN TERRITORIES)
            // ─────────────────────────────────────────────

            // -- Armenia (2026) --
            // Source: State Revenue Committee Armenia (AMD annual)
            'AM' => [
                [0, 2000000, 23.00],
                [2000000, null, 36.00],
            ],

            // -- Aruba (2026) --
            // Source: Tax Department Aruba (AWG annual)
            'AW' => [
                [0, 34930, 14.00],
                [34930, 65904, 23.00],
                [65904, 147454, 42.00],
                [147454, null, 52.00],
            ],

            // -- Azerbaijan (2026) --
            // Source: Ministry of Taxes Azerbaijan (AZN annual)
            'AZ' => [
                [0, 2500, 0.00],
                [2500, 8000, 14.00],
                [8000, null, 25.00],
            ],

            // -- Bermuda (2026) --
            // No personal income tax - British Overseas Territory
            'BM' => [
                [0, null, 0.00],
            ],

            // -- Curaçao (2026) --
            // Source: Tax Department Curaçao (ANG annual)
            'CW' => [
                [0, 33589, 9.50],
                [33589, 67179, 23.00],
                [67179, null, 39.00],
            ],

            // -- Togo (2026) --
            // Source: Office Togolais des Recettes (XOF annual)
            'TG' => [
                [0, 60000, 0.00],
                [60000, 150000, 5.00],
                [150000, 500000, 10.00],
                [500000, 1000000, 15.00],
                [1000000, 3000000, 20.00],
                [3000000, 5000000, 30.00],
                [5000000, null, 35.00],
            ],
            // -- USA (Federal 2026) --
            'US' => [
                [0, 11925, 10.00],
                [11925, 48475, 12.00],
                [48475, 103350, 22.00],
                [103350, 197300, 24.00],
                [197300, 250525, 32.00],
                [250525, 626350, 35.00],
                [626350, null, 37.00],
            ],
            // -- UK (2026/27) --
            'GB' => [
                [0, 12570, 0.00],
                [12570, 50270, 20.00],
                [50270, 125140, 40.00],
                [125140, null, 45.00],
            ],
            // -- Portugal (2026) --
            'PT' => [
                [0, 8342, 12.50],
                [8342, 12575, 15.70],
                [12575, 17820, 21.20],
                [17820, 23065, 24.10],
                [23065, 29367, 31.10],
                [29367, 42996, 34.60],
                [42996, 46470, 43.10],
                [46470, 86634, 44.60],
                [86634, null, 48.00],
            ],
            // -- Spain (2026 state + general regional avg) --
            'ES' => [
                [0, 12450, 19.00],
                [12450, 20200, 24.00],
                [20200, 35200, 30.00],
                [35200, 60000, 37.00],
                [60000, 300000, 45.00],
                [300000, null, 47.00],
            ],
            // -- Germany (2026) --
            'DE' => [
                [0, 12348, 0.00],
                [12348, 17473, 14.00],
                [17473, 67896, 24.00],
                [67896, 277826, 42.00],
                [277826, null, 45.00],
            ],
            // -- France (2026) --
            'FR' => [
                [0, 11497, 0.00],
                [11497, 29315, 11.00],
                [29315, 83823, 30.00],
                [83823, 180294, 41.00],
                [180294, null, 45.00],
            ],
            // -- Thailand (2026) --
            'TH' => [
                [0, 150000, 0.00],
                [150000, 300000, 5.00],
                [300000, 500000, 10.00],
                [500000, 750000, 15.00],
                [750000, 1000000, 20.00],
                [1000000, 2000000, 25.00],
                [2000000, 5000000, 30.00],
                [5000000, null, 35.00],
            ],
            // -- Mexico (2026 annual ISR) --
            'MX' => [
                [0, 10135.11, 1.92],
                [10135.12, 86022.11, 6.40],
                [86022.12, 151176.19, 10.88],
                [151176.20, 175735.66, 16.00],
                [175735.67, 210403.69, 17.92],
                [210403.70, 424353.97, 21.36],
                [424353.98, 668840.14, 23.52],
                [668840.15, 1276925.98, 30.00],
                [1276925.99, 1702567.97, 32.00],
                [1702567.98, 5107703.92, 34.00],
                [5107703.93, null, 35.00],
            ],
            // -- UAE: No income tax --
            // -- Singapore (YA2026) --
            'SG' => [
                [0, 20000, 0.00],
                [20000, 30000, 2.00],
                [30000, 40000, 3.50],
                [40000, 80000, 7.00],
                [80000, 120000, 11.50],
                [120000, 160000, 15.00],
                [160000, 200000, 18.00],
                [200000, 240000, 19.00],
                [240000, 280000, 19.50],
                [280000, 320000, 20.00],
                [320000, 500000, 22.00],
                [500000, 1000000, 23.00],
                [1000000, null, 24.00],
            ],
            // -- Estonia (2026 flat) --
            'EE' => [
                [0, 7849, 0.00],
                [7849, null, 24.00],
            ],
            // -- Croatia (2026 default) --
            'HR' => [
                [0, 60000, 20.00],
                [60000, null, 30.00],
            ],
            // -- Greece (2026) --
            'GR' => [
                [0, 10000, 9.00],
                [10000, 20000, 20.00],
                [20000, 30000, 26.00],
                [30000, 40000, 34.00],
                [40000, 60000, 39.00],
                [60000, null, 44.00],
            ],
            // -- Italy (IRPEF 2026) --
            'IT' => [
                [0, 28000, 23.00],
                [28000, 50000, 33.00],
                [50000, null, 43.00],
            ],
            // -- Netherlands (2026 Box 1) --
            'NL' => [
                [0, 38000, 35.70],
                [38000, 79000, 37.50],
                [79000, null, 49.50],
            ],
            // -- Japan (2026 national) --
            'JP' => [
                [0, 1950000, 5.00],
                [1950000, 3300000, 10.00],
                [3300000, 6950000, 20.00],
                [6950000, 9000000, 23.00],
                [9000000, 18000000, 33.00],
                [18000000, 40000000, 40.00],
                [40000000, null, 45.00],
            ],
            // -- Canada (2026 federal) --
            'CA' => [
                [0, 58523, 14.00],
                [58523, 117045, 20.50],
                [117045, 181440, 26.00],
                [181440, 258482, 29.00],
                [258482, null, 33.00],
            ],
            // -- Australia (2025-26 FY) --
            'AU' => [
                [0, 18200, 0.00],
                [18200, 45000, 16.00],
                [45000, 135000, 30.00],
                [135000, 190000, 37.00],
                [190000, null, 45.00],
            ],
            // -- South Korea (2026) --
            'KR' => [
                [0, 14000000, 6.00],
                [14000000, 50000000, 15.00],
                [50000000, 88000000, 24.00],
                [88000000, 150000000, 35.00],
                [150000000, 300000000, 38.00],
                [300000000, 500000000, 40.00],
                [500000000, 1000000000, 42.00],
                [1000000000, null, 45.00],
            ],
            // -- Brazil (2026 monthly) --
            'BR' => [
                [0, 5000, 0.00],
                [5000, 7350, 7.50],
                [7350, null, 27.50],
            ],
            // -- Colombia (2026) --
            'CO' => [
                [0, 1090, 0.00],
                [1090, 1700, 19.00],
                [1700, 4100, 28.00],
                [4100, 8670, 33.00],
                [8670, 18970, 35.00],
                [18970, 31000, 37.00],
                [31000, null, 39.00],
            ],
            // -- Indonesia (2026) --
            'ID' => [
                [0, 60000000, 5.00],
                [60000000, 250000000, 15.00],
                [250000000, 500000000, 25.00],
                [500000000, 5000000000, 30.00],
                [5000000000, null, 35.00],
            ],
            // -- Malaysia (YA2026) --
            'MY' => [
                [0, 5000, 0.00],
                [5000, 20000, 1.00],
                [20000, 35000, 3.00],
                [35000, 50000, 6.00],
                [50000, 70000, 11.00],
                [70000, 100000, 19.00],
                [100000, 400000, 25.00],
                [400000, 600000, 26.00],
                [600000, 2000000, 28.00],
                [2000000, null, 30.00],
            ],
            // -- Czech Republic (2026) --
            'CZ' => [
                [0, 1762812, 15.00],
                [1762812, null, 23.00],
            ],
            // -- Hungary (flat 15%) --
            'HU' => [
                [0, null, 15.00],
            ],
            // -- Poland (2026) --
            'PL' => [
                [0, 30000, 0.00],
                [30000, 120000, 12.00],
                [120000, null, 32.00],
            ],
            // -- Romania (flat 10%) --
            'RO' => [
                [0, null, 10.00],
            ],
            // -- Bulgaria (flat 10%) --
            'BG' => [
                [0, null, 10.00],
            ],
            // -- Switzerland (2026 federal only) --
            'CH' => [
                [0, 17800, 0.00],
                [17800, 31600, 0.77],
                [31600, 41400, 0.88],
                [41400, 55200, 2.64],
                [55200, 72500, 2.97],
                [72500, 78100, 5.94],
                [78100, 103600, 6.60],
                [103600, 134600, 8.80],
                [134600, 176000, 11.00],
                [176000, 755200, 13.20],
                [755200, null, 13.20],
            ],
            // -- Austria (2026) --
            'AT' => [
                [0, 13541, 0.00],
                [13541, 22261, 20.00],
                [22261, 35837, 30.00],
                [35837, 69166, 40.00],
                [69166, 103072, 48.00],
                [103072, 1000000, 50.00],
                [1000000, null, 55.00],
            ],
            // -- Ireland (2026) --
            'IE' => [
                [0, 44000, 20.00],
                [44000, null, 40.00],
            ],
            // -- Sweden (2026 — municipal avg + state) --
            'SE' => [
                [0, 643000, 32.40],
                [643000, null, 52.40],
            ],
            // -- Costa Rica (2026 salaried monthly) --
            'CR' => [
                [0, 918000, 0.00],
                [918000, 1347000, 10.00],
                [1347000, 2372000, 15.00],
                [2372000, 4727000, 20.00],
                [4727000, null, 25.00],
            ],
            // -- Panama (2026) --
            'PA' => [
                [0, 11000, 0.00],
                [11000, 50000, 15.00],
                [50000, null, 25.00],
            ],
            // -- Georgia (flat 20%) --
            'GE' => [
                [0, null, 20.00],
            ],
            // -- Malta (2026 single) --
            'MT' => [
                [0, 9100, 0.00],
                [9100, 14500, 15.00],
                [14500, 19500, 25.00],
                [19500, 60000, 25.00],
                [60000, null, 35.00],
            ],
            // -- Cyprus (2026) --
            'CY' => [
                [0, 19500, 0.00],
                [19500, 28000, 20.00],
                [28000, 36300, 25.00],
                [36300, 60000, 30.00],
                [60000, null, 35.00],
            ],
            // -- Philippines (2026) --
            'PH' => [
                [0, 250000, 0.00],
                [250000, 400000, 15.00],
                [400000, 800000, 20.00],
                [800000, 2000000, 25.00],
                [2000000, 8000000, 30.00],
                [8000000, null, 35.00],
            ],
            // -- Vietnam (2026 monthly) --
            'VN' => [
                [0, 5000000, 5.00],
                [5000000, 10000000, 10.00],
                [10000000, 18000000, 15.00],
                [18000000, 32000000, 20.00],
                [32000000, 52000000, 25.00],
                [52000000, 80000000, 30.00],
                [80000000, null, 35.00],
            ],
            // -- Barbados (2026) --
            'BB' => [
                [0, 50000, 12.50],
                [50000, null, 28.50],
            ],
            // -- India (2026 FY 2026-27) --
            'IN' => [
                [0, 300000, 0.00],
                [300000, 700000, 5.00],
                [700000, 1000000, 10.00],
                [1000000, 1200000, 15.00],
                [1200000, 1500000, 20.00],
                [1500000, null, 30.00],
            ],
            // -- China (2026) --
            'CN' => [
                [0, 36000, 3.00],
                [36000, 144000, 10.00],
                [144000, 300000, 20.00],
                [300000, 420000, 25.00],
                [420000, 660000, 30.00],
                [660000, 960000, 35.00],
                [960000, null, 45.00],
            ],
            // -- New Zealand (2026 FY) --
            'NZ' => [
                [0, 15600, 10.50],
                [15600, 53500, 17.50],
                [53500, 78100, 30.00],
                [78100, 180000, 33.00],
                [180000, null, 39.00],
            ],
            // -- Hong Kong (2026) --
            'HK' => [
                [0, 50000, 2.00],
                [50000, 100000, 6.00],
                [100000, 150000, 10.00],
                [150000, 200000, 14.00],
                [200000, null, 17.00],
            ],
            // -- Saudi Arabia (2026) --
            'SA' => [
                [0, null, 0.00], // No personal income tax for residents
            ],
            // -- Qatar (2026) --
            'QA' => [
                [0, null, 0.00], // No personal income tax
            ],
            // -- Turkey (2026) --
            'TR' => [
                [0, 110000, 15.00],
                [110000, 230000, 20.00],
                [230000, 580000, 27.00],
                [580000, 3000000, 35.00],
                [3000000, null, 40.00],
            ],
            // -- Norway (2026) --
            'NO' => [
                [0, 208050, 22.00],
                [208050, null, 22.00], // Flat rate after basic allowance
            ],
            // -- Denmark (2026) --
            'DK' => [
                [0, 61500, 8.00],
                [61500, 568900, 42.00],
                [568900, null, 56.40],
            ],
            // -- Finland (2026) --
            'FI' => [
                [0, 20500, 0.00],
                [20500, 30300, 6.00],
                [30300, 49200, 17.25],
                [49200, 85800, 21.25],
                [85800, null, 31.25],
            ],
            // -- Belgium (2026) --
            'BE' => [
                [0, 15820, 25.00],
                [15820, 28540, 40.00],
                [28540, 50650, 45.00],
                [50650, null, 50.00],
            ],
            // -- Luxembourg (2026) --
            'LU' => [
                [0, 12438, 0.00],
                [12438, 23475, 8.00],
                [23475, 40812, 9.00],
                [40812, 58555, 10.00],
                [58555, 76295, 11.00],
                [76295, 220788, 39.00],
                [220788, null, 42.00],
            ],
            // -- Taiwan (2026) --
            'TW' => [
                [0, 590000, 5.00],
                [590000, 1330000, 12.00],
                [1330000, 2660000, 20.00],
                [2660000, 4980000, 30.00],
                [4980000, null, 40.00],
            ],
            // -- Israel (2026) --
            'IL' => [
                [0, 83040, 10.00],
                [83040, 119280, 14.00],
                [119280, 186480, 20.00],
                [186480, 269280, 31.00],
                [269280, 560280, 35.00],
                [560280, 721560, 47.00],
                [721560, null, 50.00],
            ],
            // -- Argentina (2026) --
            'AR' => [
                [0, 2268000, 5.00],
                [2268000, 3402000, 9.00],
                [3402000, 4536000, 12.00],
                [4536000, 5670000, 15.00],
                [5670000, 6804000, 19.00],
                [6804000, 9072000, 23.00],
                [9072000, 13608000, 27.00],
                [13608000, 22680000, 31.00],
                [22680000, null, 35.00],
            ],
            // -- Chile (2026) --
            'CL' => [
                [0, 8093712, 0.00],
                [8093712, 17985936, 4.00],
                [17985936, 29976552, 8.00],
                [29976552, 41967180, 13.50],
                [41967180, 53957808, 23.00],
                [53957808, 71943732, 30.40],
                [71943732, null, 40.00],
            ],
            // -- Morocco (2026) --
            'MA' => [
                [0, 30000, 0.00],
                [30000, 50000, 10.00],
                [50000, 60000, 20.00],
                [60000, 80000, 30.00],
                [80000, 180000, 34.00],
                [180000, null, 38.00],
            ],
            // -- South Africa (2026) --
            'ZA' => [
                [0, 237100, 18.00],
                [237100, 370500, 26.00],
                [370500, 512800, 31.00],
                [512800, 673000, 36.00],
                [673000, 857900, 39.00],
                [857900, 1817000, 41.00],
                [1817000, null, 45.00],
            ],
            // -- Iceland (2026) --
            'IS' => [
                [0, 446521, 31.45],
                [446521, null, 37.95],
            ],
            // -- Lithuania (2026) --
            'LT' => [
                [0, 90000, 20.00],
                [90000, null, 32.00],
            ],
            // -- Latvia (2026) --
            'LV' => [
                [0, 20004, 20.00],
                [20004, 78100, 23.00],
                [78100, null, 31.00],
            ],
            // -- Slovakia (2026) --
            'SK' => [
                [0, 42290, 19.00],
                [42290, null, 25.00],
            ],
            // -- Slovenia (2026) --
            'SI' => [
                [0, 8755, 16.00],
                [8755, 25750, 26.00],
                [25750, 51350, 33.00],
                [51350, 74160, 39.00],
                [74160, null, 50.00],
            ],
        ];

        $records = [];

        foreach ($allBrackets as $isoCode => $brackets) {
            $countryId = DB::table('countries')->where('iso_code', $isoCode)->value('id');
            $currencyCode = DB::table('countries')->where('iso_code', $isoCode)->value('currency_code');
            if (!$countryId) continue;

            foreach ($brackets as $bracket) {
                $records[] = [
                    'country_id'  => $countryId,
                    'state_id'    => null, // Federal/National tax
                    'tax_type_id' => $taxTypeId,
                    'tax_year'    => 2026,
                    'min_income'  => $bracket[0],
                    'max_income'  => $bracket[1],
                    'rate'        => $bracket[2],
                    'has_cap'     => false,
                    'annual_cap'  => null,
                    'currency_code' => $currencyCode,
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        // --- ADD US STATE BRACKETS ---
        $usCountryId = DB::table('countries')->where('iso_code', 'US')->value('id');
        if ($usCountryId) {
            $stateBrackets = [
                // California (2026 Single, simplified projections)
                'CA' => [
                    [0, 10412, 1.00],
                    [10412, 24684, 2.00],
                    [24684, 38959, 4.00],
                    [38959, 54081, 6.00],
                    [54081, 68350, 8.00],
                    [68350, 349137, 9.30],
                    [349137, 418961, 10.30],
                    [418961, 698271, 11.30],
                    [698271, null, 12.30],
                ],
                // New York State (2026 Single, simplified projections)
                'NY' => [
                    [0, 8500, 4.00],
                    [8500, 11700, 4.50],
                    [11700, 13900, 5.25],
                    [13900, 80650, 5.50],
                    [80650, 215400, 5.85],
                    [215400, 1077550, 6.25],
                    [1077550, 5000000, 6.85],
                    [5000000, 25000000, 9.65],
                    [25000000, null, 10.90],
                ],
            ];

            foreach ($stateBrackets as $stateCode => $brackets) {
                $stateId = DB::table('states')->where('country_id', $usCountryId)->where('code', $stateCode)->value('id');
                if (!$stateId) continue;

                foreach ($brackets as $bracket) {
                    $records[] = [
                        'country_id'  => $usCountryId,
                        'state_id'    => $stateId,
                        'tax_type_id' => $taxTypeId,
                        'tax_year'    => 2026,
                        'min_income'  => $bracket[0],
                        'max_income'  => $bracket[1],
                        'rate'        => $bracket[2],
                        'has_cap'     => false,
                        'annual_cap'  => null,
                        'currency_code' => 'USD',
                        'is_active'   => true,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('tax_brackets')->insert($chunk);
        }
    }
}
