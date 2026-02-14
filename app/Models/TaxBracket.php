<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxBracket extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'tax_type_id',
        'tax_year',
        'min_income',
        'max_income',
        'rate',
        'has_cap',
        'annual_cap',
        'is_active',
    ];

    protected $casts = [
        'min_income' => 'decimal:2',
        'max_income' => 'decimal:2',
        'rate'       => 'decimal:2',
        'has_cap'    => 'boolean',
        'annual_cap' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function taxType()
    {
        return $this->belongsTo(TaxType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('tax_year', $year);
    }
}
