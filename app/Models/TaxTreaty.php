<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxTreaty extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_a_id',
        'country_b_id',
        'treaty_type',
        'applicable_tax_year',
        'description',
        'is_active',
    ];

    protected $casts = [
        'applicable_tax_year' => 'integer',
        'is_active' => 'boolean',
    ];

    public function countryA()
    {
        return $this->belongsTo(Country::class, 'country_a_id');
    }

    public function countryB()
    {
        return $this->belongsTo(Country::class, 'country_b_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBetween($query, $country1Id, $country2Id)
    {
        return $query->where(function ($q) use ($country1Id, $country2Id) {
            $q->where('country_a_id', $country1Id)->where('country_b_id', $country2Id);
        })->orWhere(function ($q) use ($country1Id, $country2Id) {
            $q->where('country_a_id', $country2Id)->where('country_b_id', $country1Id);
        });
    }
}
