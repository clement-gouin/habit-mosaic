<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Tracker;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tracker>
 */
class TrackerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->word(),
            'icon' => fake()->randomElement(['bath', 'spoon', 'chair', 'jar', 'toilet', 'soap', 'sink', 'shower', 'tv', 'faucet', 'blender', 'plug']),
            'unit' => fake()->boolean() ? fake()->randomElement(['kilometer', 'meter', 'step', 'time', 'hour', 'minute', 'second']) : null,
            'value_step' => fake()->randomElement([.1, .5, 1, 5, 10]),
            'default_value' => 0,
            'target_value' => fake()->randomFloat(max: 30),
            'target_score' => fake()->randomFloat(min: -10, max: 10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
