<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialSecurityRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'tax_year',
        'contribution_type',
        'fund_name',
        'rate',
        'min_income',
        'max_income',
        'annual_cap',
        'currency_code',
        'is_active',
    ];

    protected $casts = [
        'rate'       => 'decimal:2',
        'min_income' => 'decimal:2',
        'max_income' => 'decimal:2',
        'annual_cap' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    /**
     * Get the country this rule belongs to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Scope for active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for a specific country and tax year.
     */
    public function scopeForCountryYear($query, int $countryId, int $year)
    {
        return $query->where('country_id', $countryId)->where('tax_year', $year);
    }

    /**
     * Scope for a specific tax year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('tax_year', $year);
    }

    /**
     * Scope for employee contributions only.
     */
    public function scopeEmployee($query)
    {
        return $query->where('contribution_type', 'employee');
    }

    /**
     * Scope for employer contributions only.
     */
    public function scopeEmployer($query)
    {
        return $query->where('contribution_type', 'employer');
    }

    /**
     * Calculate the contribution for a given income.
     */
    public function calculateContribution(float $income): float
    {
        $rate = (float) $this->rate / 100;
        $minIncome = (float) $this->min_income;
        $maxIncome = $this->max_income ? (float) $this->max_income : PHP_FLOAT_MAX;

        // Only apply to income within the range
        $taxableIncome = min($income, $maxIncome) - $minIncome;

        if ($taxableIncome <= 0) {
            return 0;
        }

        $contribution = $taxableIncome * $rate;

        // Apply annual cap if set
        if ($this->annual_cap !== null) {
            $contribution = min($contribution, (float) $this->annual_cap);
        }

        return round($contribution, 2);
    }
}
