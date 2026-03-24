<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        $name = fake()->unique()->country();

        return [
            'name'                => $name,
            'iso_code'            => fake()->unique()->countryCode(),
            'iso_code_3'          => strtoupper(fake()->unique()->lexify('???')),
            'currency_code'       => fake()->currencyCode(),
            'currency_symbol'     => '$',
            'slug'                => \Illuminate\Support\Str::slug($name),
            'has_progressive_tax' => true,
            'flat_tax_rate'       => null,
            'standard_deduction'  => null,
            'taxes_worldwide_income' => true,
            'has_digital_nomad_visa' => false,
            'tax_residency_days'  => 183,
            'worldwide_income_threshold' => null,
            'counts_arrival_day'  => true,
            'counts_departure_day' => true,
            'considers_center_of_vital_interests' => false,
            'is_active'           => true,
        ];
    }

    /**
     * Country with flat tax (no progressive brackets).
     */
    public function flatTax(float $rate = 15.0): static
    {
        return $this->state(fn () => [
            'has_progressive_tax' => false,
            'flat_tax_rate'       => $rate,
        ]);
    }

    /**
     * Country with zero tax.
     */
    public function zeroTax(): static
    {
        return $this->state(fn () => [
            'has_progressive_tax' => false,
            'flat_tax_rate'       => 0,
            'has_digital_nomad_visa' => true,
        ]);
    }

    /**
     * US-specific country record.
     */
    public function us(): static
    {
        return $this->state(fn () => [
            'name'                => 'United States',
            'iso_code'            => 'US',
            'iso_code_3'          => 'USA',
            'currency_code'       => 'USD',
            'currency_symbol'     => '$',
            'slug'                => 'united-states',
            'has_progressive_tax' => true,
            'flat_tax_rate'       => null,
            'standard_deduction'  => 15700,
            'tax_residency_days'  => 183,
            'worldwide_income_threshold' => 0, // US citizens taxed worldwide regardless of days
        ]);
    }

    /**
     * Country that doesn't count arrival day.
     */
    public function noArrivalDay(): static
    {
        return $this->state(fn () => [
            'counts_arrival_day' => false,
        ]);
    }

    /**
     * Country that doesn't count departure day.
     */
    public function noDepartureDay(): static
    {
        return $this->state(fn () => [
            'counts_departure_day' => false,
        ]);
    }
}
