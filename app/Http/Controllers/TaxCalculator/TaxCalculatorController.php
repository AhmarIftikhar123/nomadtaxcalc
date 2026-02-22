<?php

namespace App\Http\Controllers\TaxCalculator;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxCalculator\StoreStep1Request;
use App\Http\Requests\TaxCalculator\StoreStep2Request;
use App\Models\Country;
use App\Models\TaxBracket;
use App\Models\TaxType;
use App\Models\UserCalculation;
use App\Services\CurrencyService;
use App\Services\TaxCalculator\TaxCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
}

