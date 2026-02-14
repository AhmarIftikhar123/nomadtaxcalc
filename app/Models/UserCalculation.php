<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserCalculation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'session_uuid',
        'step_reached',
        'started_at',
        'completed_at',
        'completed_calculation',
        'country_id',
        'ip_address',
        'gross_income',
        'currency',
        'citizenship_country_code',
        'additional_inputs',
        'included_tax_types',
        'taxable_income',
        'total_tax',
        'net_income',
        'effective_tax_rate',
        'tax_breakdown',
        'residency_warnings',
        'treaty_applied',
        'feie_result',
        'device_type',
        'referrer',
    ];

    protected $casts = [
        'additional_inputs'      => 'array',
        'included_tax_types'     => 'array',
        'tax_breakdown'          => 'array',
        'residency_warnings'     => 'array',
        'treaty_applied'         => 'array',
        'feie_result'            => 'array',
        'completed_calculation'  => 'boolean',
        'started_at'             => 'datetime',
        'completed_at'           => 'datetime',
    ];

    /**
     * Boot function to auto-generate session_uuid
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->session_uuid) {
                $model->session_uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the countries visited for this calculation
     */
    public function countriesVisited()
    {
        return $this->hasMany(UserCalculationCountry::class);
    }

    /**
     * Get the citizenship country
     */
    public function citizenshipCountry()
    {
        return $this->belongsTo(Country::class, 'citizenship_country_code', 'iso_code');
    }

    /**
     * Get the primary country
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
