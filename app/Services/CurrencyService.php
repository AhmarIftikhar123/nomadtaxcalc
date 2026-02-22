<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    protected string $apiUrl;
    protected int    $ttl; // cache minutes

    public function __construct()
    {
        $this->apiUrl = rtrim(config('currency.api_url', 'https://api.frankfurter.app'), '/');
        $this->ttl    = (int) config('currency.cache_ttl', 1440);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * All available currencies from the frankfurter API.
     * Returns [ ['value' => 'EUR', 'label' => 'EUR — Euro'], ... ]
     * Returns [] on any failure (safe fallback for frontend).
     */
    public function getAvailableCurrencies(): array
    {
        return Cache::remember('currency.list', now()->addMinutes($this->ttl), function () {
            try {
                $response = Http::timeout(5)->get("{$this->apiUrl}/currencies");

                if (!$response->successful()) {
                    return [];
                }

                $data = $response->json(); // ['EUR' => 'Euro', 'USD' => 'US Dollar', ...]

                $availableCurrencies = collect($data)
                    ->map(fn($name, $code) => [
                        'value' => $code,
                        'label' => "{$code} — {$name}",
                    ])
                    ->values()
                    ->toArray();
                        
                return $availableCurrencies;
            } catch (\Throwable $e) {
                Log::warning('CurrencyService::getAvailableCurrencies failed', [
                    'error' => $e->getMessage(),
                ]);
                return [];
            }
        });
    }

    /**
     * Convert an amount from one currency to another.
     * Returns the **converted** amount.
     * Falls back to returning $amount unchanged (rate = 1) on any failure.
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to || $amount == 0) {
            return $amount;
        }

        $rate = $this->getRate($from, $to);
        return round($amount * $rate, 2);
    }

    /**
     * Exchange rate from $from → $to currency.
     * Cached per pair for 24h. Falls back to 1.0 on failure.
     */
    public function getRate(string $from, string $to): float
    {
        if ($from === $to) {
            return 1.0;
        }

        $cacheKey = "currency.rate.{$from}.{$to}";

        return Cache::remember($cacheKey, now()->addMinutes($this->ttl), function () use ($from, $to) {
            try {
                $response = Http::timeout(5)->get("{$this->apiUrl}/latest", [
                    'from'   => $from,
                    'to'     => $to,
                    'amount' => 1,
                ]);

                if (!$response->successful()) {
                    return 1.0;
                }

                $rates = $response->json('rates', []);
                return isset($rates[$to]) ? (float) $rates[$to] : 1.0;

            } catch (\Throwable $e) {
                Log::warning("CurrencyService::getRate({$from}->{$to}) failed", [
                    'error' => $e->getMessage(),
                ]);
                return 1.0; // safe fallback — no conversion applied
            }
        });
    }
}
