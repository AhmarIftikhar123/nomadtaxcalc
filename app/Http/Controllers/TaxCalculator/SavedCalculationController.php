<?php

namespace App\Http\Controllers\TaxCalculator;

use App\Http\Controllers\Controller;
use App\Models\UserCalculation;
use Inertia\Inertia;

class SavedCalculationController extends Controller
{
    /**
     * List all saved calculations for the authenticated user.
     */
    public function index()
    {
        $calculations = UserCalculation::where('user_id', auth()->id())
            ->with('country:id,name,iso_code')
            ->latest()
            ->get()
            ->map(fn ($c) => [
                'id'                => $c->id,
                'tax_year'          => $c->tax_year,
                'currency'          => $c->currency,
                'gross_income'      => $c->gross_income,
                'total_tax'         => $c->total_tax,
                'net_income'        => $c->net_income,
                'effective_tax_rate'=> $c->effective_tax_rate,
                'citizenship_country_name' => $c->country?->name ?? 'Unknown',
                'citizenship_country_code' => $c->citizenship_country_code,
                'saved_at'          => $c->created_at?->toDateString(),
            ]);

        return Inertia::render('MyCalculations/Index', [
            'calculations' => $calculations,
        ]);
    }

    /**
     * Soft-delete a saved calculation.
     */
    public function destroy(UserCalculation $calculation)
    {
        // Ensure the calculation belongs to the authenticated user
        if ($calculation->user_id !== auth()->id()) {
            abort(403);
        }

        $calculation->delete();

        return back()->with('success', 'Calculation deleted.');
    }
}
