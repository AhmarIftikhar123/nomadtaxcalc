<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'key',
        'name',
        'description',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all tax brackets for this tax type
     */
    public function taxBrackets()
    {
        return $this->hasMany(TaxBracket::class);
    }

    /**
     * Scope for active tax types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for default tax types (auto-included in calculations)
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
