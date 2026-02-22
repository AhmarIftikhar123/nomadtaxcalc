<?php

namespace Database\Factories;

use App\Models\UserCalculation;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserCalculationFactory extends Factory
{
    protected $model = UserCalculation::class;

    public function definition(): array
    {
        return [
            'user_id'                  => null, // null = anonymous / unsaved
            'gross_income'             => 100000,
            'currency'                 => 'USD',
            'country_id'               => Country::factory(),
            'citizenship_country_code' => 'US',
            'step_reached'             => 1,
            'tax_year'                 => 2026,
            'ip_address'               => fake()->ipv4(),
            'device_type'              => 'desktop',
            'started_at'               => now(),
        ];
    }

    /**
     * Calculation that has completed step 2.
     */
    public function step2Completed(): static
    {
        return $this->state(fn () => [
            'step_reached'          => 2,
            'completed_calculation' => false,
        ]);
    }

    /**
     * Calculation saved by an authenticated user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn () => [
            'user_id'               => $user->id,
            'step_reached'          => 3,
            'completed_calculation' => true,
            'completed_at'          => now(),
        ]);
    }
}
