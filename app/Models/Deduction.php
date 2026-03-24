<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'tax_year',
        'deduction_type',
        'filing_status',
        'amount',
        'is_percentage',
        'phase_out_start',
        'phase_out_end',
        'currency_code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'is_percentage'   => 'boolean',
        'phase_out_start' => 'decimal:2',
        'phase_out_end'   => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    /**
     * Get the country this deduction belongs to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Scope for active deductions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for a specific tax year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('tax_year', $year);
    }

    /**
     * Calculate the effective deduction amount, accounting for phase-outs.
     */
    public function getEffectiveAmount(float $income): float
    {
        $amount = (float) $this->amount;

        // If percentage-based, calculate from income
        if ($this->is_percentage) {
            $amount = $income * ($amount / 100);
        }

        // Apply phase-out if applicable
        if ($this->phase_out_start !== null && $this->phase_out_end !== null) {
            $phaseOutStart = (float) $this->phase_out_start;
            $phaseOutEnd   = (float) $this->phase_out_end;

            if ($income >= $phaseOutEnd) {
                return 0;
            }

            if ($income > $phaseOutStart) {
                $phaseOutRange = $phaseOutEnd - $phaseOutStart;
                $incomeOverStart = $income - $phaseOutStart;
                $reductionRatio = $incomeOverStart / $phaseOutRange;
                $amount = $amount * (1 - $reductionRatio);
            }
        }

        return max(0, round($amount, 2));
    }
}
