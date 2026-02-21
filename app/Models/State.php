<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'code',
        'has_income_tax',
        'is_active',
    ];

    protected $casts = [
        'has_income_tax' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the country that owns the state.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the tax brackets for the state.
     */
    public function taxBrackets()
    {
        return $this->hasMany(TaxBracket::class);
    }

    /**
     * Scope for active states.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
