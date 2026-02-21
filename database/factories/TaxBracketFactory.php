<?php

namespace Database\Factories;

use App\Models\TaxBracket;
use App\Models\Country;
use App\Models\TaxType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxBracketFactory extends Factory
{
    protected $model = TaxBracket::class;

    public function definition(): array
    {
        return [
            'country_id'  => Country::factory(),
            'tax_type_id' => TaxType::factory(),
            'tax_year'    => 2026,
            'min_income'  => 0,
            'max_income'  => 50000,
            'rate'        => 10.00,
            'has_cap'     => false,
            'annual_cap'  => null,
            'is_active'   => true,
        ];
    }

    /**
     * Bracket with a cap.
     */
    public function withCap(float $cap): static
    {
        return $this->state(fn () => [
            'has_cap'    => true,
            'annual_cap' => $cap,
        ]);
    }
}
