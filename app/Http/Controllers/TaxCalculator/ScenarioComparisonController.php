<?php

namespace App\Http\Controllers\TaxCalculator;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\TaxBracket;
use App\Models\TaxType;
use App\Services\TaxCalculator\ScenarioComparisonService;
use App\Services\TaxCalculator\TaxCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ScenarioComparisonController extends Controller
{
    public function __construct(
        protected TaxCalculatorService $taxCalculatorService,
        protected ScenarioComparisonService $comparisonService,
    ) {}

    /**
     * Render the comparison page.
     * Pre-fills Scenario A from session data (if available).
     */
    public function index()
    {
        if (!session('tax_calc_step1')) {
            return redirect()->route('tax-calculator.index', ['scenario_comparison' => 'true']);
        }
        $countries      = $this->taxCalculatorService->getCountries();
        $currencies     = $this->taxCalculatorService->getCurrencies();
        $availableYears = TaxBracket::select('tax_year')
            ->distinct()
            ->where('is_active', true)
            ->orderByDesc('tax_year')
            ->pluck('tax_year')
            ->toArray();
        $taxTypes = TaxType::systemDefaults()->active()->orderBy('sort_order')->get();
        $states   = State::active()->select('id', 'country_id', 'name', 'code')->orderBy('name')->get();

        return Inertia::render('TaxCalculator/Compare', [
            'countries'      => $countries,
            'states'         => $states,
            'currencies'     => $currencies,
            'availableYears' => $availableYears,
            'taxTypes'       => $taxTypes,
            'prefillStep1'   => session('tax_calc_step1'),
            'prefillPeriods' => session('tax_calc_step2', []),
        ]);
    }
    /**
     * Run both scenarios through the tax pipeline and return JSON.
     */
    public function compare(Request $request): JsonResponse
    {
        $request->validate([
            'step1'      => 'required|array',
            'scenarioA'  => 'required|array',
            'scenarioB'  => 'required|array',
        ]);

        try {
            $result = $this->comparisonService->compare(
                $request->input('step1'),
                $request->input('scenarioA'),
                $request->input('scenarioB'),
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Scenario comparison error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'We encountered an error comparing scenarios. Please adjust your inputs and try again.',
            ], 422);
        }
    }
}
