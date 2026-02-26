<?php

namespace App\Http\Controllers\TaxCalculator;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxCalculator\StoreStep1Request;
use App\Http\Requests\TaxCalculator\StoreStep2Request;
use App\Mail\TaxResultsMail;
use App\Models\Country;
use App\Models\TaxBracket;
use App\Models\TaxType;
use App\Models\UserCalculation;
use App\Services\CurrencyService;
use App\Services\TaxCalculator\TaxCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TaxCalculatorController extends Controller
{
    public function __construct(
        protected TaxCalculatorService $taxCalculatorService,
        protected CurrencyService $currencyService,
    ) {}

    /**
     * Single-page tax calculator.
     * Loads prefill data from:
     *   - Session       (guest / normal flow)
     *   - DB via ?calculation_id=X (auth user editing a saved record)
     */
    public function index(Request $request)
    {
        $countries      = $this->taxCalculatorService->getCountries();
        $currencies     = $this->taxCalculatorService->getCurrencies();
        $availableYears = TaxBracket::select('tax_year')
            ->distinct()
            ->where('is_active', true)
            ->orderByDesc('tax_year')
            ->pluck('tax_year')
            ->toArray();
        $taxTypes = TaxType::systemDefaults()->active()->orderBy('sort_order')->get();
        $states   = \App\Models\State::active()->select('id', 'country_id', 'name', 'code')->orderBy('name')->get();

        $savedStep1Data        = null;
        $savedResidencyPeriods = [];
        $calculationResult     = session('tax_calc_result');
        $editingCalculationId  = null;

        // ── Edit mode: load from DB via query string ─────────────────────────
        $calculationId = $request->query('calculation_id');

        if ($calculationId && auth()->check()) {
            $calculation = UserCalculation::where('id', $calculationId)
                ->where('user_id', auth()->id())
                ->with('countriesVisited.country')
                ->first();

            if ($calculation) {
                $prefill               = $this->taxCalculatorService->rebuildPrefillFromCalculation($calculation);
                $savedStep1Data        = $prefill['step1'];
                $savedResidencyPeriods = $prefill['periods'];
                $editingCalculationId  = $calculation->id;
                $calculationResult     = null; // Force re-calculation
            }
        } else {
            // ── Session mode: normal / guest flow ────────────────────────────
            $step1 = session('tax_calc_step1');
            if ($step1) {
                $savedStep1Data = $step1;
            }
            $savedResidencyPeriods = session('tax_calc_step2', []);
        }

        return Inertia::render('TaxCalculator/Index', [
            'countries'            => $countries,
            'states'               => $states,
            'currencies'           => $currencies,
            'availableYears'       => $availableYears,
            'taxTypes'             => $taxTypes,
            'savedStep1Data'       => $savedStep1Data,
            'savedResidencyPeriods'=> $savedResidencyPeriods,
            'calculationResult'    => $calculationResult,
            'currentStep'          => 1,
            'editingCalculationId' => $editingCalculationId,
        ]);
    }

    /**
     * Store Step 1 data in session only. No DB write.
     */
    public function storeStep1(StoreStep1Request $request)
    {
        $payload = $this->taxCalculatorService->buildSessionStep1Payload(
            $request->only(['annual_income', 'currency', 'citizenship_country_id', 'tax_year', 'domicile_state_id'])
        );

        session(['tax_calc_step1' => $payload]);
        // Clear any previous step 2 / result when step 1 is re-submitted
        session()->forget(['tax_calc_step2', 'tax_calc_result']);

        return redirect()->route('tax-calculator.index');
    }

    /**
     * Store Step 2 data in session, run the calculation pipeline, store result.
     * No DB write.
     */
    public function storeStep2(StoreStep2Request $request)
    {
        $step1 = session('tax_calc_step1');

        if (!$step1) {
            return redirect()->route('tax-calculator.index')
                ->with('error', 'Session expired. Please start from Step 1.');
        }

        try {
            $periods = $request->residency_periods;
            session(['tax_calc_step2' => $periods]);

            // ── Cache key: hash of step1 + periods input ──────────────────
            $cacheKey = 'tax_calc_result_' . md5(json_encode($step1) . json_encode($periods));

            $result = Cache::remember($cacheKey, now()->addHour(), function () use ($step1, $periods) {
                return $this->taxCalculatorService->calculateTaxesFromSession($step1, $periods);
            });

            session(['tax_calc_result' => $result]);

            return back()->with(['calculationResult' => $result]);

        } catch (\Exception $e) {
            \Log::error('Tax Calculation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'calculation' => 'We encountered an error while calculating your taxes. Please try again or adjust your residency periods.',
            ]);
        }
    }

    /**
     * Save (or update) the current calculation for the authenticated user.
     * All business logic is in TaxCalculatorService.
     */
    public function saveCalculation(Request $request)
    {
        $step1   = session('tax_calc_step1');
        $periods = session('tax_calc_step2');
        $result  = session('tax_calc_result');

        $saved = $this->taxCalculatorService->saveCalculationForUser(
            auth()->id(),
            $step1,
            $periods ?? [],
            $result,
            $request->input('calculation_id') // null = create, ID = update
        );

        return back()
            ->with('success', $request->input('calculation_id')
                ? 'Calculation updated successfully.'
                : 'Calculation saved to your account.')
            ->with('saved_calculation_id', $saved->id);
    }

    /**
     * Email the current calculation results to the authenticated user.
     * Auto-generates (or refreshes) the share token so the email CTA
     * always contains a valid link.
     */
    public function sendEmail(Request $request)
    {
        $calculationId = $request->input('calculation_id');

        $calculation = UserCalculation::where('id', $calculationId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Ensure the calculation has an active share token for the email CTA
        if (!$calculation->isShareActive()) {
            $calculation->update([
                'share_token'      => Str::random(64),
                'share_expires_at' => now()->addMonth(),
            ]);
        }

        $shareUrl = route('tax-calculator.shared', $calculation->share_token);

        Mail::to(auth()->user()->email)
            ->send(new TaxResultsMail($calculation, $shareUrl));

        $calculation->update(['email_sent_at' => now()]);

        return back()->with('success', 'Results sent to ' . auth()->user()->email);
    }

    /**
     * Generate (or refresh) a 30-day shareable read-only link.
     */
    public function generateLink(Request $request)
    {
        $calculationId = $request->input('calculation_id');

        $calculation = UserCalculation::where('id', $calculationId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Always refresh token + expiry so the link is always 30 days from now
        $calculation->update([
            'share_token'      => Str::random(64),
            'share_expires_at' => now()->addMonth(),
        ]);

        $shareUrl = route('tax-calculator.shared', $calculation->share_token);

        return back()->with('share_url', $shareUrl);
    }

    /**
     * Public read-only view of a shared calculation.
     * No auth required — the token acts as the access credential.
     */
    public function viewShared(string $token)
    {
        $calculation = UserCalculation::where('share_token', $token)
            ->with('countriesVisited.country')
            ->first();

        // Token doesn't exist
        if (!$calculation) {
            return Inertia::render('SharedCalculation/Show', [
                'expired' => true,
                'result'  => null,
            ]);
        }

        // Token is expired
        if (!$calculation->isShareActive()) {
            return Inertia::render('SharedCalculation/Show', [
                'expired'  => true,
                'expiredAt' => $calculation->share_expires_at?->format('F j, Y'),
                'result'   => null,
            ]);
        }

        // Build the result array from stored JSON columns
        $result = [
            'annual_income'       => $calculation->taxable_income,
            'currency'            => $calculation->currency,
            'tax_year'            => $calculation->tax_year,
            'total_tax'           => $calculation->total_tax,
            'net_income'          => $calculation->net_income,
            'effective_tax_rate'  => $calculation->effective_tax_rate,
            'breakdown_by_country'=> $calculation->tax_breakdown ?? [],
            'residency_warnings'  => $calculation->residency_warnings ?? [],
            'residency_data'      => [],
            'comparison_data'     => $this->buildComparisonFromBreakdown($calculation->tax_breakdown ?? []),
            'treaties_applied'    => $calculation->treaty_applied ?? [],
            'feie_result'         => $calculation->feie_result,
            'recommendations'     => [],
        ];

        return Inertia::render('SharedCalculation/Show', [
            'expired'            => false,
            'result'             => $result,
            'citizenshipCode'    => $calculation->citizenship_country_code,
            'shareExpiresAt'     => $calculation->share_expires_at?->format('F j, Y'),
        ]);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function buildComparisonFromBreakdown(array $breakdown): array
    {
        $comparison = array_map(fn ($bd) => [
            'country'    => $bd['country_name'] ?? '',
            'liability'  => $bd['tax_due'] ?? 0,
            'percentage' => $bd['effective_rate'] ?? 0,
        ], $breakdown);

        usort($comparison, fn ($a, $b) => $b['liability'] <=> $a['liability']);

        return $comparison;
    }
}

