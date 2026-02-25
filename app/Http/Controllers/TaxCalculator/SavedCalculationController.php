<?php

namespace App\Http\Controllers\TaxCalculator;

use App\Http\Controllers\Controller;
use App\Models\UserCalculation;
use App\Services\MyCalculations\SavedCalculationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SavedCalculationController extends Controller
{
    public function __construct(
        private readonly SavedCalculationService $service
    ) {}

    /**
     * List saved calculations, with optional DB-level search + pagination.
     * Query params: ?search=&page=
     */
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->value();

        $calculations = $this->service->forUser(auth()->id(), $search ?: null);

        return Inertia::render('MyCalculations/Index', [
            'calculations' => $calculations,
            'filters'      => ['search' => $search],
        ]);
    }

    /**
     * Soft-delete a saved calculation.
     */
    public function destroy(UserCalculation $calculation)
    {
        $this->service->delete($calculation, auth()->id());

        return back()->with('success', 'Calculation deleted.');
    }
}
