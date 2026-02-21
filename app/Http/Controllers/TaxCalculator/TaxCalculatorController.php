<?php

namespace App\Http\Controllers\TaxCalculator;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxCalculator\StoreStep1Request;
use App\Http\Requests\TaxCalculator\StoreStep2Request;
use App\Models\Country;
use App\Models\TaxBracket;
use App\Models\TaxType;
use App\Models\UserCalculation;
use App\Services\TaxCalculator\TaxCalculatorService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaxCalculatorController extends Controller
{
    public function __construct(protected TaxCalculatorService $taxCalculatorService) {}

    /**
     * Single-page tax calculator — serves all 3 steps from one route.
     * The frontend manages step transitions locally.
     */
    public function index()
    {
        $countries = $this->taxCalculatorService->getCountries();
        $currencies = $this->taxCalculatorService->getCurrencies();

        // Get distinct tax years from tax_brackets table (dynamic)
        $availableYears = TaxBracket::select('tax_year')
            ->distinct()
            ->where('is_active', true)
            ->orderByDesc('tax_year')
            ->pluck('tax_year')
            ->toArray();

        // Get tax types for step 2
        $taxTypes = TaxType::systemDefaults()->active()->orderBy('sort_order')->get();

        // Load saved calculation data (for prefill on reload / fresh visit)
        $savedStep1Data = null;
        $savedResidencyPeriods = [];
        $calculationResult = session('calculationResult');
        $currentStep = 1;

        $sessionUuid = session('calculation_session_uuid');

        if ($sessionUuid) {
            $calculation = UserCalculation::where('session_uuid', $sessionUuid)
                ->with('countriesVisited.country')
                ->first();

            if ($calculation) {
                // Note: Always start at step 1 on a fresh page load.
                // Saved data is for prefilling only — step transitions
                // are managed on the frontend via preserveState POSTs.

                // Build step 1 prefill data
                $citizenshipCountry = Country::find($calculation->country_id);
                $savedStep1Data = [
                    'annual_income' => $calculation->gross_income,
                    'currency' => $calculation->currency,
                    'tax_year' => $calculation->tax_year,
                    'citizenship_country_id' => $citizenshipCountry?->id ?? '',
                    'citizenship_country_name' => $citizenshipCountry?->name ?? 'Unknown',
                    'citizenship_country_code' => $calculation->citizenship_country_code,
                    'domicile_state_id' => $calculation->domicile_state_id ?? '',
                ];

                // Build step 2 prefill data
                if ($calculation->countriesVisited->isNotEmpty()) {
                    $savedResidencyPeriods = $calculation->countriesVisited->map(function ($visit) {
                        // Rebuild selected_tax_types from stored DB data
                        $selectedTaxTypes = [];
                        $storedIds = $visit->selected_tax_type_ids ?? [];
                        $overrides = $visit->tax_type_overrides ?? [];

                        foreach ($storedIds as $taxTypeId) {
                            if (is_string($taxTypeId) && str_starts_with($taxTypeId, 'custom_')) {
                                // Custom tax entry
                                $override = $overrides[$taxTypeId] ?? [];
                                $selectedTaxTypes[] = [
                                    'id' => crc32($taxTypeId), // deterministic ID from key
                                    'tax_type_id' => '',
                                    'custom_name' => $override['name'] ?? 'Custom Tax',
                                    'amount_type' => $override['type'] ?? 'percentage',
                                    'amount' => $override['amount'] ?? '',
                                    'is_custom' => true,
                                ];
                            } else {
                                // Predefined tax type
                                $override = $overrides[$taxTypeId] ?? $overrides[(string) $taxTypeId] ?? [];
                                $selectedTaxTypes[] = [
                                    'id' => (int) $taxTypeId * 1000 + $visit->id, // unique ID
                                    'tax_type_id' => (string) $taxTypeId,
                                    'custom_name' => '',
                                    'amount_type' => $override['type'] ?? 'percentage',
                                    'amount' => $override['amount'] ?? '',
                                    'is_custom' => false,
                                ];
                            }
                        }

                        return [
                            'id' => $visit->id,
                            'country_id' => $visit->country_id,
                            'state_id' => $visit->state_id ?? '',
                            'country_name' => $visit->country?->name ?? '',
                            'country_code' => $visit->country?->iso_code ?? '',
                            'days' => $visit->days_spent,
                            'isTaxResident' => (bool) $visit->is_tax_resident,
                            'selected_tax_types' => $selectedTaxTypes,
                        ];
                    })->toArray();
                }

                // Results are NOT auto-computed on fresh load.
                // They are computed when the user submits Step 2,
                // and returned via the storeStep2 redirect.
            }
        }

        $states = \App\Models\State::active()->select('id', 'country_id', 'name', 'code')->orderBy('name')->get();

        return Inertia::render('TaxCalculator/Index', [
            'countries' => $countries,
            'states' => $states,
            'currencies' => $currencies,
            'availableYears' => $availableYears,
            'taxTypes' => $taxTypes,
            'savedStep1Data' => $savedStep1Data,
            'savedResidencyPeriods' => $savedResidencyPeriods,
            'calculationResult' => $calculationResult,
            'currentStep' => $currentStep,
        ]);
    }

    /**
     * Store Step 1 data (annual income, currency, citizenship)
     * Returns to Index with step advanced to 2.
     */
    public function storeStep1(StoreStep1Request $request)
    {
        $sessionUuid = session('calculation_session_uuid');

        $calculation = $this->taxCalculatorService->saveStep1Data(
            $request->only(['annual_income', 'currency', 'citizenship_country_id', 'tax_year', 'domicile_state_id']),
            $sessionUuid
        );

        // Store session UUID for subsequent steps
        session(['calculation_session_uuid' => $calculation->session_uuid]);

        // Advance step
        $calculation->update(['step_reached' => 2]);

        return redirect()->route('tax-calculator.index');
    }

    /**
     * Store Step 2 data (countries visited), calculate taxes.
     * Returns to Index with step advanced to 3 (results computed on next load).
     */
    public function storeStep2(StoreStep2Request $request)
    {
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

        // Advance to step 3
        $calculation->update(['step_reached' => 3]);

        // Calculate results immediately and return them (so preserveState works)
        $result = $this->taxCalculatorService->calculateTaxes($calculation);

        return back()->with(['calculationResult' => $result]);
    }
}
