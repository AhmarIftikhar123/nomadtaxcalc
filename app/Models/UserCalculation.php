<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCalculation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'step_reached',
        'started_at',
        'completed_at',
        'completed_calculation',
        'country_id',
        'ip_address',
        'gross_income',
        'currency',
        'tax_year',
        'citizenship_country_code',
        'domicile_state_id',
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
        'share_token',
        'share_expires_at',
        'email_sent_at',
    ];

    protected $casts = [
        'additional_inputs'     => 'array',
        'included_tax_types'    => 'array',
        'tax_breakdown'         => 'array',
        'residency_warnings'    => 'array',
        'treaty_applied'        => 'array',
        'feie_result'           => 'array',
        'completed_calculation' => 'boolean',
        'started_at'            => 'datetime',
        'completed_at'          => 'datetime',
        'share_expires_at'      => 'datetime',
        'email_sent_at'         => 'datetime',
        'tax_year'              => 'integer',
    ];

    /**
     * The authenticated user who saved this calculation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * True when the share link exists and has not yet expired.
     */
    public function isShareActive(): bool
    {
        return $this->share_token !== null
            && $this->share_expires_at !== null
            && $this->share_expires_at->isFuture();
    }

    /**
     * Get the countries visited for this calculation.
     */
    public function countriesVisited()
    {
        return $this->hasMany(UserCalculationCountry::class);
    }

    /**
     * Get the citizenship country.
     */
    public function citizenshipCountry()
    {
        return $this->belongsTo(Country::class, 'citizenship_country_code', 'iso_code');
    }

    /**
     * Get the domicile state.
     */
    public function domicileState()
    {
        return $this->belongsTo(State::class, 'domicile_state_id');
    }

    /**
     * Get the primary (citizenship) country.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
