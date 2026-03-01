<?php

namespace App\Services\TaxCalculator;

use Illuminate\Support\Facades\Cache;

class ScenarioComparisonService
{
    public function __construct(
        protected TaxCalculatorService $taxCalculatorService,
    ) {}

    /**
     * Compare two travel scenarios using the same Step 1 inputs.
     *
     * Each scenario's result is cached individually so that
     * re-comparing after changing only one scenario is fast.
     */
    public function compare(array $step1, array $periodsA, array $periodsB): array
    {
        $resultA = $this->cachedCalculation($step1, $periodsA);
        $resultB = $this->cachedCalculation($step1, $periodsB);

        return [
            'resultA' => $resultA,
            'resultB' => $resultB,
            'diff'    => $this->buildDiff($resultA, $resultB),
        ];
    }

    // ─── Cached wrapper ──────────────────────────────────────────────────────

    private function cachedCalculation(array $step1, array $periods): array
    {
        $cacheKey = 'scenario_compare_' . md5(json_encode($step1) . json_encode($periods));

        return Cache::remember($cacheKey, now()->addHour(), function () use ($step1, $periods) {
            return $this->taxCalculatorService->calculateTaxesFromSession($step1, $periods);
        });
    }

    // ─── Diff builder ────────────────────────────────────────────────────────

    private function buildDiff(array $resultA, array $resultB): array
    {
        $taxDelta   = $resultA['total_tax'] - $resultB['total_tax'];
        $rateDelta  = $resultA['effective_tax_rate'] - $resultB['effective_tax_rate'];
        $incomeDelta = $resultB['net_income'] - $resultA['net_income'];

        // Determine winner (lower total_tax wins)
        if ($resultA['total_tax'] < $resultB['total_tax']) {
            $winner = 'A';
        } elseif ($resultB['total_tax'] < $resultA['total_tax']) {
            $winner = 'B';
        } else {
            $winner = 'tie';
        }

        return [
            'winner'      => $winner,
            'savings'     => abs($taxDelta),
            'taxDelta'    => round($taxDelta, 2),
            'rateDelta'   => round($rateDelta, 2),
            'incomeDelta' => round($incomeDelta, 2),
            'perCountry'  => $this->buildPerCountryDelta(
                $resultA['breakdown_by_country'] ?? [],
                $resultB['breakdown_by_country'] ?? [],
            ),
        ];
    }

    /**
     * Merge breakdown arrays from both scenarios by country, computing deltas.
     */
    private function buildPerCountryDelta(array $breakdownA, array $breakdownB): array
    {
        $mapA = collect($breakdownA)->keyBy('country_code')->toArray();
        $mapB = collect($breakdownB)->keyBy('country_code')->toArray();

        $allCodes = array_unique(array_merge(array_keys($mapA), array_keys($mapB)));

        $deltas = [];
        foreach ($allCodes as $code) {
            $a = $mapA[$code] ?? null;
            $b = $mapB[$code] ?? null;

            $taxA  = $a['tax_due'] ?? 0;
            $taxB  = $b['tax_due'] ?? 0;
            $delta = $taxB - $taxA;

            $deltas[] = [
                'country_code' => $code,
                'country_name' => $a['country_name'] ?? $b['country_name'] ?? $code,
                'daysA'        => $a['days_spent'] ?? null,
                'daysB'        => $b['days_spent'] ?? null,
                'residentA'    => $a['is_tax_resident'] ?? false,
                'residentB'    => $b['is_tax_resident'] ?? false,
                'taxA'         => round($taxA, 2),
                'taxB'         => round($taxB, 2),
                'delta'        => round($delta, 2),
            ];
        }

        return $deltas;
    }
}
