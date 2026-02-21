<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCalculationCountry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_calculation_id',
        'country_id',
        'state_id',
        'days_spent',
        'is_tax_resident',
        'allocated_income',
        'taxable_income',
        'tax_due',
        'tax_by_type',
        'selected_tax_type_ids',
        'tax_type_overrides',
    ];

    protected $casts = [
        'is_tax_resident'       => 'boolean',
        'tax_by_type'           => 'array',
        'selected_tax_type_ids' => 'array',
        'tax_type_overrides'    => 'array',
    ];

    /**
     * Get the calculation this belongs to
     */
    public function userCalculation()
    {
        return $this->belongsTo(UserCalculation::class);
    }

    /**
     * Get the country
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
