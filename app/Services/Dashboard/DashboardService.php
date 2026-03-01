<?php

namespace App\Services\Dashboard;

use App\Models\UserCalculation;
use App\Models\UserCalculationCountry;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Aggregate stat-card numbers for a given user.
     */
    public function getStatsForUser(int $userId): array
    {
        $calculations = UserCalculation::where('user_id', $userId)
            ->where('completed_calculation', true);

        $totalCalculations = (clone $calculations)->count();

        if ($totalCalculations === 0) {
            return [
                'totalCalculations' => 0,
                'avgEffectiveRate'  => 0,
                'countriesAnalyzed' => 0,
                'savedCalculations' => 0,
            ];
        }

        $avgRate = (clone $calculations)->avg('effective_tax_rate') ?? 0;

        // Distinct countries from the pivot table
        $countriesAnalyzed = UserCalculationCountry::whereIn(
            'user_calculation_id',
            UserCalculation::where('user_id', $userId)->pluck('id')
        )->distinct('country_id')->count('country_id');

        return [
            'totalCalculations' => $totalCalculations,
            'avgEffectiveRate'  => round($avgRate, 1),
            'countriesAnalyzed' => $countriesAnalyzed,
            'savedCalculations' => $totalCalculations, // all completed are saved
        ];
    }

    /**
     * Latest completed calculations with country relationships.
     */
    public function getRecentCalculations(int $userId, int $limit = 5): array
    {
        return UserCalculation::where('user_id', $userId)
            ->where('completed_calculation', true)
            ->with('countriesVisited.country')
            ->orderByDesc('completed_at')
            ->limit($limit)
            ->get()
            ->map(function ($calc) {
                $countries = $calc->countriesVisited
                    ->map(fn ($cv) => [
                        'name' => $cv->country?->name ?? '–',
                        'code' => $cv->country?->iso_code ?? '',
                    ])
                    ->values()
                    ->toArray();

                return [
                    'id'              => $calc->id,
                    'tax_year'        => $calc->tax_year,
                    'currency'        => $calc->currency,
                    'gross_income'    => $calc->gross_income,
                    'total_tax'       => $calc->total_tax,
                    'net_income'      => $calc->net_income,
                    'effective_rate'  => round($calc->effective_tax_rate, 1),
                    'countries'       => $countries,
                    'completed_at'    => $calc->completed_at?->format('M j, Y'),
                    'has_share_link'  => $calc->isShareActive(),
                    'email_sent'      => $calc->email_sent_at !== null,
                ];
            })
            ->toArray();
    }

    /**
     * Calculations grouped by tax_year — for the small bar chart.
     */
    public function getYearBreakdown(int $userId): array
    {
        return UserCalculation::where('user_id', $userId)
            ->where('completed_calculation', true)
            ->select('tax_year', DB::raw('COUNT(*) as count'))
            ->groupBy('tax_year')
            ->orderByDesc('tax_year')
            ->pluck('count', 'tax_year')
            ->toArray();
    }

    /**
     * Most frequently visited countries across all calculations.
     */
    public function getTopCountries(int $userId, int $limit = 5): array
    {
        return UserCalculationCountry::query()
            ->join('user_calculations', 'user_calculation_countries.user_calculation_id', '=', 'user_calculations.id')
            ->join('countries', 'user_calculation_countries.country_id', '=', 'countries.id')
            ->where('user_calculations.user_id', $userId)
            ->whereNull('user_calculations.deleted_at')
            ->select('countries.name', 'countries.iso_code', DB::raw('COUNT(*) as count'))
            ->groupBy('countries.name', 'countries.iso_code')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'name'  => $row->name,
                'code'  => $row->iso_code,
                'count' => $row->count,
            ])
            ->toArray();
    }
}
