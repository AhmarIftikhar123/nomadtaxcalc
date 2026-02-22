<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    public function __construct(protected CurrencyService $currencyService) {}

    /**
     * Return all available currencies from the frankfurter API (cached 24h).
     * Called lazily from the frontend when a territorial country is added.
     */
    public function index(): JsonResponse
    {
        $currencies = $this->currencyService->getAvailableCurrencies();

        return response()->json($currencies);
    }
}
