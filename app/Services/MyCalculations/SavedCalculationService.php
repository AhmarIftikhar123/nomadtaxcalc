<?php

namespace App\Services\MyCalculations;

use App\Models\UserCalculation;
use Illuminate\Pagination\LengthAwarePaginator;

class SavedCalculationService
{
    private const PER_PAGE = 10;

    /**
     * Return a paginated, mapped collection of calculations for the given user.
     * Optionally filter by country name, tax year, or currency.
     */
    public function forUser(int $userId, ?string $search = null): LengthAwarePaginator
    {
        $query = UserCalculation::where('user_id', $userId)
            ->with('country:id,name,iso_code')
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('country', fn ($r) => $r->where('name', 'like', "%{$search}%"))
                  ->orWhere('tax_year', 'like', "%{$search}%")
                  ->orWhere('currency',  'like', "%{$search}%");
            });
        }

        return $query->paginate(self::PER_PAGE)->through(fn ($c) => [
            'id'                       => $c->id,
            'tax_year'                 => $c->tax_year,
            'currency'                 => $c->currency,
            'gross_income'             => $c->gross_income,
            'total_tax'                => $c->total_tax,
            'net_income'               => $c->net_income,
            'effective_tax_rate'       => $c->effective_tax_rate,
            'citizenship_country_name' => $c->country?->name ?? 'Unknown',
            'citizenship_country_code' => $c->citizenship_country_code,
            'saved_at'                 => $c->created_at?->toDateString(),
        ]);
    }

    /**
     * Authorise and soft-delete a calculation.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function delete(UserCalculation $calculation, int $userId): void
    {
        if ($calculation->user_id !== $userId) {
            abort(403, 'This calculation does not belong to you.');
        }

        $calculation->delete();
    }
}
