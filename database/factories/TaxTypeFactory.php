<?php

namespace Database\Factories;

use App\Models\TaxType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxTypeFactory extends Factory
{
    protected $model = TaxType::class;

    public function definition(): array
    {
        return [
            'key'         => fake()->unique()->slug(2),
            'name'        => fake()->words(3, true),
            'description' => fake()->sentence(),
            'is_default'  => false,
            'is_active'   => true,
            'sort_order'  => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Income tax (default type, always included).
     */
    public function incomeTax(): static
    {
        return $this->state(fn () => [
            'key'        => 'income_tax',
            'name'       => 'Income Tax',
            'is_default' => true,
            'sort_order' => 1,
        ]);
    }

    /**
     * Social security tax type.
     */
    public function socialSecurity(): static
    {
        return $this->state(fn () => [
            'key'        => 'social_security',
            'name'       => 'Social Security / National Insurance',
            'is_default' => false,
            'sort_order' => 2,
        ]);
    }
}
