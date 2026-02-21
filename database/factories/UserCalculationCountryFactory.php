<?php

namespace Database\Factories;

use App\Models\UserCalculationCountry;
use App\Models\UserCalculation;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserCalculationCountryFactory extends Factory
{
    protected $model = UserCalculationCountry::class;

    public function definition(): array
    {
        return [
            'user_calculation_id'  => UserCalculation::factory(),
            'country_id'           => Country::factory(),
            'days_spent'           => 183,
            'is_tax_resident'      => true,
        ];
    }

    /**
     * Non-resident visitor.
     */
    public function nonResident(int $days = 90): static
    {
        return $this->state(fn () => [
            'days_spent'      => $days,
            'is_tax_resident' => false,
        ]);
    }
}
