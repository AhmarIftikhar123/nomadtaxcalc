<?php

namespace App\Http\Controllers\TaxCalculator;

use App\Http\Controllers\Controller;
use App\Models\UserCalculation;
use App\Services\TaxCalculator\TaxCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class TaxCalculatorController extends Controller
{
    public function __construct(protected TaxCalculatorService $taxCalculatorService) {}

    /**
     * Show the tax calculator index with Step 1
     */
    public function index()
    {
        $countries = $this->taxCalculatorService->getCountries();
        $currencies = $this->taxCalculatorService->getCurrencies();

        return Inertia::render('TaxCalculator/Index', [
            'countries' => $countries,
            'currencies' => $currencies,
            'currentStep' => 1,
        ]);
    }

    /**
     * Store Step 1 data (annual income, currency, citizenship)
     */
    public function storeStep1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'annual_income' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'citizenship_country_id' => 'required|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get existing session UUID from session or create new calculation
        $sessionUuid = session('calculation_session_uuid');

        $calculation = $this->taxCalculatorService->saveStep1Data(
            $request->only(['annual_income', 'currency', 'citizenship_country_id']),
            $sessionUuid
        );

        // Store session UUID for subsequent steps
        session(['calculation_session_uuid' => $calculation->session_uuid]);

        return redirect()->route('tax-calculator.step-2');
    }

    /**
     * Show the tax calculator step 2
     */
    public function step2()
    {
        // Check if user has completed step 1
        $sessionUuid = session('calculation_session_uuid');

        if (!$sessionUuid) {
            return redirect()->route('tax-calculator.index')
                ->with('error', 'Please complete Step 1 first.');
        }

        $calculation = UserCalculation::where('session_uuid', $sessionUuid)->first();

        if (!$calculation) {
            return redirect()->route('tax-calculator.index')
                ->with('error', 'Session expired. Please start again.');
        }

        $countries = $this->taxCalculatorService->getCountries();

        // Load the citizenship country name for step1 summary display
        $citizenshipCountry = \App\Models\Country::where('iso_code', $calculation->citizenship_country_code)->first();

        return Inertia::render('TaxCalculator/Step2', [
            'countries' => $countries,
            'step1Data' => [
                'annual_income' => $calculation->gross_income,
                'currency' => $calculation->currency,
                'citizenship_country_code' => $calculation->citizenship_country_code,
                'citizenship_country_name' => $citizenshipCountry?->name ?? 'Unknown',
            ],
            'currentStep' => 2,
        ]);
    }

    /**
     * Store Step 2 data (countries visited)
     */
    public function storeStep2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'residency_periods' => 'required|array|min:1',
            'residency_periods.*.country_id' => 'required|exists:countries,id|distinct',
            'residency_periods.*.days' => 'required|integer|min:1|max:365',
        ]);

        // Custom validation: total days must equal 365
        $validator->after(function ($validator) use ($request) {
            $totalDays = collect($request->residency_periods)->sum('days');
            if ($totalDays !== 365) {
                $validator->errors()->add('residency_periods', "Total days must equal 365. Current total: {$totalDays}");
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sessionUuid = session('calculation_session_uuid');

        if (!$sessionUuid) {
            return redirect()->route('tax-calculator.index')
                ->with('error', 'Session expired. Please start from Step 1.');
        }

        $calculation = UserCalculation::where('session_uuid', $sessionUuid)->first();

        if (!$calculation) {
            return redirect()->route('tax-calculator.index')
                ->with('error', 'Calculation not found. Please start again.');
        }

        $this->taxCalculatorService->saveStep2Data(
            $calculation,
            $request->residency_periods
        );

        return redirect()->route('tax-calculator.step-3');
    }

    /**
     * Show the tax calculator step 3 with results
     */
    public function step3()
    {
        $sessionUuid = session('calculation_session_uuid');

        if (!$sessionUuid) {
            return redirect()->route('tax-calculator.index')
                ->with('error', 'Please complete all steps from the beginning.');
        }

        $calculation = UserCalculation::where('session_uuid', $sessionUuid)
            ->with('countriesVisited')
            ->first();

        if (!$calculation || $calculation->countriesVisited->isEmpty()) {
            return redirect()->route('tax-calculator.step-2')
                ->with('error', 'Please complete Step 2 first.');
        }

        // Calculate taxes
        $result = $this->taxCalculatorService->calculateTaxes($calculation);

        return Inertia::render('TaxCalculator/Step3', [
            'result' => $result,
            'currentStep' => 3,
        ]);
    }
}
