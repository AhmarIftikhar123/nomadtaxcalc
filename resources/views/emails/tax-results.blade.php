<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Tax Results — {{ $calculation->tax_year }}</title>
    <style>
        /* ── Design tokens matching the app's tailwind.config.js ── */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', 'Figtree', Arial, sans-serif;
            background-color: #faf8f7; /* light */
            color: #22262a;            /* primary */
            line-height: 1.6;
        }
        .wrapper { padding: 40px 16px; }
        .container {
            max-width: 620px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        /* Header */
        .header {
            background-color: #18191a; /* dark */
            padding: 28px 32px;
            text-align: center;
        }
        .header img { max-height: 40px; filter: brightness(0) invert(1); }
        .header-fallback { color: #ffffff; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; }

        /* Hero section */
        .hero {
            background-color: #22262a;
            padding: 36px 32px;
            text-align: center;
        }
        .hero-label { font-size: 14px; color: #9ca0a3; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; font-weight: 600; }
        .hero-year  { font-size: 16px; color: #c0c3c6; margin-bottom: 20px; font-weight: 500; }
        .hero-title { font-size: 36px; font-weight: 800; color: #ffffff; margin-bottom: 4px; }

        /* Metrics strip */
        .metrics {
            display: table;
            width: 100%;
            border-collapse: collapse;
            background: #faf8f7;
            border-bottom: 1px solid #e0e0e1;
        }
        .metric-cell {
            display: table-cell;
            width: 25%;
            padding: 18px 12px;
            text-align: center;
            border-right: 1px solid #e0e0e1;
            vertical-align: middle;
        }
        .metric-cell:last-child { border-right: none; }
        .metric-label { font-size: 10px; color: #737578; text-transform: uppercase; letter-spacing: 0.8px; }
        .metric-value { font-size: 16px; font-weight: 800; color: #22262a; margin-top: 4px; }
        .metric-value.highlight { color: #22262a; }

        /* Body content */
        .body-content { padding: 32px; }

        h2 { font-size: 15px; font-weight: 700; color: #22262a; margin-bottom: 14px; margin-top: 28px; text-transform: uppercase; letter-spacing: 0.5px; }
        h2:first-child { margin-top: 0; }

        /* Country breakdown table */
        .breakdown-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .breakdown-table th {
            text-align: left;
            padding: 8px 10px;
            background: #f5f4f3;
            border-bottom: 2px solid #e0e0e1;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #737578;
        }
        .breakdown-table td {
            padding: 10px 10px;
            border-bottom: 1px solid #f0eeec;
            color: #22262a;
        }
        .breakdown-table tr:last-child td { border-bottom: none; }
        .tax-amount { font-weight: 700; }

        /* FEIE / Treaty tags */
        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 4px;
        }
        .tag-feie    { background: #dcfce7; color: #166534; }
        .tag-treaty  { background: #dbeafe; color: #1e40af; }

        /* Info box */
        .info-box {
            background: #f5f4f3;
            border-left: 4px solid #22262a;
            border-radius: 6px;
            padding: 14px 16px;
            font-size: 13px;
            color: #22262a;
            margin-top: 12px;
        }
        .info-box strong { display: block; margin-bottom: 4px; }

        /* CTA Button */
        .cta-wrap    { text-align: center; margin: 32px 0 20px; }
        .cta-button  {
            display: inline-block;
            background: #22262a;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 8px;
            letter-spacing: 0.2px;
        }

        /* Disclaimer */
        .disclaimer {
            background: #fff8e1;
            border: 1px solid #ffd54f;
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 12px;
            color: #5d4037;
            margin-top: 24px;
            line-height: 1.5;
        }

        /* Footer */
        .footer {
            background: #faf8f7;
            border-top: 1px solid #e0e0e1;
            padding: 20px 32px;
            text-align: center;
            font-size: 12px;
            color: #737578;
            line-height: 1.8;
        }
        .footer a { color: #737578; text-decoration: underline; }

        @media (max-width: 480px) {
            .metric-cell { display: block; width: 50%; float: left; }
            .hero-title  { font-size: 22px; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">

        {{-- ── Header ────────────────────────────────────── --}}
        <div class="header">
            @if(file_exists(resource_path('js/assets/images/logos/logo-desktop.png')))
                <img src="{{ $message->embed(resource_path('js/assets/images/logos/logo-desktop.png')) }}" alt="{{ env('APP_NAME') }}">
            @else
                <div class="header-fallback">{{ env('APP_NAME') }}</div>
            @endif
        </div>

        {{-- ── Hero ─────────────────────────────────────── --}}
        <div class="hero">
            <div class="hero-label">Tax Calculation Results</div>
            <div class="hero-year">Tax Year {{ $calculation->tax_year }}</div>
            <div class="hero-title">
                {{ $calculation->currency }} {{ number_format($calculation->total_tax, 0) }}
                <span style="font-size:16px;font-weight:400;color:#a0a3a6"> total tax</span>
            </div>
        </div>

        {{-- ── Key Metrics Strip ──────────────────────────── --}}
        <div class="metrics">
            <div class="metric-cell">
                <div class="metric-label">Gross Income</div>
                <div class="metric-value">{{ $calculation->currency }} {{ number_format($calculation->gross_income, 0) }}</div>
            </div>
            <div class="metric-cell">
                <div class="metric-label">Total Tax</div>
                <div class="metric-value">{{ $calculation->currency }} {{ number_format($calculation->total_tax, 0) }}</div>
            </div>
            <div class="metric-cell">
                <div class="metric-label">Eff. Rate</div>
                <div class="metric-value">{{ number_format($calculation->effective_tax_rate, 1) }}%</div>
            </div>
            <div class="metric-cell">
                <div class="metric-label">Net Income</div>
                <div class="metric-value">{{ $calculation->currency }} {{ number_format($calculation->net_income, 0) }}</div>
            </div>
        </div>

        {{-- ── Body ──────────────────────────────────────── --}}
        <div class="body-content">

            {{-- Per-Country Tax Breakdown --}}
            @if(!empty($calculation->tax_breakdown))
                <h2>Tax Breakdown by Country</h2>
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>Days</th>
                            <th>Taxable Income</th>
                            <th>Tax Due</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calculation->tax_breakdown as $bd)
                            <tr>
                                <td>
                                    {{ $bd['country_name'] ?? '–' }}
                                    @if(!empty($bd['feie_applied']))
                                        <span class="tag tag-feie">FEIE</span>
                                    @endif
                                    @if(!empty($bd['treaty_credit_applied']))
                                        <span class="tag tag-treaty">Treaty</span>
                                    @endif
                                </td>
                                <td>{{ $bd['days_spent'] ?? '–' }}</td>
                                <td>{{ $calculation->currency }} {{ number_format($bd['taxable_income'] ?? 0, 0) }}</td>
                                <td class="tax-amount">{{ $calculation->currency }} {{ number_format($bd['tax_due'] ?? 0, 0) }}</td>
                                <td>{{ number_format($bd['effective_rate'] ?? 0, 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            {{-- FEIE Status --}}
            @php
                $feie = $calculation->feie_result;
            @endphp
            @if(!empty($feie) && !empty($feie['eligible']))
                <h2>FEIE Applied</h2>
                <div class="info-box">
                    <strong>Foreign Earned Income Exclusion (FEIE)</strong>
                    You qualified for FEIE in {{ $calculation->tax_year }}.
                    Excluded income: <strong>{{ $calculation->currency }} {{ number_format($feie['excluded_income'] ?? 0, 0) }}</strong>
                    (limit: {{ $calculation->currency }} {{ number_format($feie['limit'] ?? 0, 0) }}).
                </div>
            @endif

            {{-- Treaties --}}
            @php
                $treaties = $calculation->treaty_applied;
            @endphp
            @if(!empty($treaties) && count($treaties) > 0)
                <h2>Treaties Applied</h2>
                @foreach($treaties as $treaty)
                    @php
                        $countries  = $treaty['countries'] ?? [];
                        $treatyType = $treaty['type'] ?? 'credit';
                        $taxSaved   = $treaty['tax_saved'] ?? ($treaty['savings'] ?? 0);
                        $label      = implode(' – ', $countries);
                        $typeLabel  = $treatyType === 'credit' ? 'Foreign Tax Credit' : 'Exemption';
                    @endphp
                    <div class="info-box" style="margin-bottom: 8px;">
                        <strong>{{ $label }} Treaty ({{ $typeLabel }})</strong>
                        This treaty mitigated double taxation on your global income.
                        @if(!empty($taxSaved))
                            &#8212; Estimated savings:
                            <strong>{{ $calculation->currency }} {{ number_format($taxSaved, 0) }}</strong>
                        @endif
                    </div>
                @endforeach
            @endif

            {{-- CTA --}}
            <div class="cta-wrap">
                <a href="{{ $shareUrl }}" class="cta-button">View Full Interactive Results →</a>
            </div>
            <p style="font-size:12px;color:#737578;text-align:center;margin-top:8px;">
                This link is valid until {{ $calculation->share_expires_at?->format('F j, Y') ?? 'N/A' }}.
            </p>

            {{-- Disclaimer --}}
            <div class="disclaimer">
                ⚠ <strong>Disclaimer:</strong> These results are estimates for informational purposes only and do not constitute professional tax advice. Tax laws change frequently. Please consult a qualified tax professional before making financial decisions.
            </div>

        </div>

        {{-- ── Footer ────────────────────────────────────── --}}
        <div class="footer">
            <p>You requested this email from <strong>{{ env('APP_NAME') }}</strong>.</p>
            <p>© {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
        </div>

    </div>
</div>
</body>
</html>
