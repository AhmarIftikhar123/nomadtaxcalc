<?php

namespace Database\Factories;

use App\Models\TaxTreaty;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxTreatyFactory extends Factory
{
    protected $model = TaxTreaty::class;

    public function definition(): array
    {
        return [
            'country_a_id'         => Country::factory(),
            'country_b_id'         => Country::factory(),
            'treaty_type'          => 'credit',
            'applicable_tax_year'  => 2026,
            'description'          => fake()->sentence(),
            'is_active'            => true,
        ];
    }

    public function credit(): static
    {
        return $this->state(fn () => ['treaty_type' => 'credit']);
    }

    public function exemption(): static
    {
        return $this->state(fn () => ['treaty_type' => 'exemption']);
    }

    public function partial(): static
    {
        return $this->state(fn () => ['treaty_type' => 'partial']);
    }
}
