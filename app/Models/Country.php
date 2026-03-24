<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Basic Information
        'name',
        'iso_code',
        'iso_code_3',
        'currency_code',
        'currency_symbol',
        // Tax System
        'has_progressive_tax',
        'flat_tax_rate',
        'standard_deduction',
        'taxes_worldwide_income',
        'tax_basis',
        // Digital Nomad
        'has_digital_nomad_visa',
        'digital_nomad_visa_name',
        'min_income_for_visa',
        'visa_income_period',
        // Residency Rules
        'tax_residency_days',
        'worldwide_income_threshold',
        'counts_arrival_day',
        'counts_departure_day',
        'considers_center_of_vital_interests',
        // Additional
        'tax_system_notes',
        'visa_requirements',
        'avg_monthly_cost_of_living',
        'official_tax_authority_url',
        // SEO & Content
        'slug',
        'meta_description',
        'is_featured',
        'popularity_rank',
        // Status
        'is_active',
        'data_last_updated',
    ];

    protected $casts = [
        'has_progressive_tax'               => 'boolean',
        'taxes_worldwide_income'            => 'boolean',
        'has_digital_nomad_visa'            => 'boolean',
        'counts_arrival_day'                => 'boolean',
        'counts_departure_day'              => 'boolean',
        'considers_center_of_vital_interests' => 'boolean',
        'is_featured'                       => 'boolean',
        'is_active'                         => 'boolean',
        'flat_tax_rate'                     => 'decimal:2',
        'standard_deduction'                => 'decimal:2',
        'min_income_for_visa'               => 'decimal:2',
        'avg_monthly_cost_of_living'        => 'decimal:2',
        'worldwide_income_threshold'        => 'integer',
    ];

    /**
     * Scope for active countries
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get tax brackets for this country
     */
    public function taxBrackets()
    {
        return $this->hasMany(TaxBracket::class);
    }

    /**
     * Get states for this country
     */
    public function states()
    {
        return $this->hasMany(State::class);
    }

    /**
     * Get all user calculations for this country
     */
    public function userCalculations()
    {
        return $this->hasMany(UserCalculation::class, 'citizenship_country_code', 'iso_code');
    }

    /**
     * Get all calculation countries using this country
     */
    public function calculationCountries()
    {
        return $this->hasMany(UserCalculationCountry::class);
    }

    /**
     * Get deductions for this country
     */
    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    /**
     * Get social security rules for this country
     */
    public function socialSecurityRules()
    {
        return $this->hasMany(SocialSecurityRule::class);
    }
}
