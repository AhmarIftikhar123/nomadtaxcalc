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
        'local_income',
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
        'local_income'          => 'float',
        'tax_by_type'           => 'array',
        'selected_tax_type_ids' => 'array',
        'tax_type_overrides'    => 'array',
    ];

    public function userCalculation()
    {
        return $this->belongsTo(UserCalculation::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
