<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
    ) {}

    public function index()
    {
        $userId = auth()->id();

        return Inertia::render('Dashboard', [
            'stats'              => $this->dashboardService->getStatsForUser($userId),
            'recentCalculations' => $this->dashboardService->getRecentCalculations($userId),
            'yearBreakdown'      => $this->dashboardService->getYearBreakdown($userId),
            'topCountries'       => $this->dashboardService->getTopCountries($userId),
        ]);
    }
}
