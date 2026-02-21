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

    public function scopeBetween($query, $countryOneId, $countryTwoId)
    {
        return $query->where(function ($q) use ($countryOneId, $countryTwoId) {
            $q->where('country_a_id', $countryOneId)
                ->where('country_b_id', $countryTwoId);
        })->orWhere(function ($q) use ($countryOneId, $countryTwoId) {
            $q->where('country_a_id', $countryTwoId)
                ->where('country_b_id', $countryOneId);
        });
    }
}
