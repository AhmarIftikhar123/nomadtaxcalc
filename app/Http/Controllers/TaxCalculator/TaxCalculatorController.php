<?php

namespace App\Http\Controllers\TaxCalculator;

use App\Http\Controllers\Controller;
use App\Services\TaxCalculator\TaxCalculatorService;
use Illuminate\Http\Request;
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
     * Show the tax calculator step 2
     */
    public function step2()
    {
        return Inertia::render('TaxCalculator/Step2');
    }
}
