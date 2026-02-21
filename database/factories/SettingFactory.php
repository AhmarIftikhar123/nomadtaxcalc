<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'key'         => fake()->unique()->slug(2),
            'value'       => fake()->word(),
            'type'        => 'string',
            'description' => fake()->sentence(),
        ];
    }

    /**
     * FEIE amount setting.
     */
    public function feieAmount(float $amount = 126500): static
    {
        return $this->state(fn () => [
            'key'   => 'feie_amount_2026',
            'value' => (string) $amount,
            'type'  => 'integer',
        ]);
    }

    /**
     * FEIE min days setting.
     */
    public function feieMinDays(int $days = 330): static
    {
        return $this->state(fn () => [
            'key'   => 'feie_min_days',
            'value' => (string) $days,
            'type'  => 'integer',
        ]);
    }
}
