<?php

namespace Database\Factories;

use App\Models\Tracker;
use App\Models\User;
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
        $data = [
            'user_id' => User::factory(),
            'category_id' => null,
            'name' => fake()->unique()->word(),
            'icon' => fake()->randomElement(['bath', 'spoon', 'chair', 'jar', 'toilet', 'soap', 'sink', 'shower', 'tv', 'faucet', 'blender', 'plug']),
            'order' => fn () => Tracker::count() + 1,
            'target_score' => fake()->randomFloat(min: -10, max: 10),
        ];

        if (fake()->boolean()) {
            return [
                ...$data,
                'unit' => null,
                'value_step' => 1,
                'target_value' => 1,
                'single' => true,
                'overflow' => false,
            ];
        } else {
            return [
                ...$data,
                'unit' => fake()->randomElement(['kilometer', 'meter', 'step', 'time', 'hour', 'minute', 'second']),
                'value_step' => fake()->randomElement([.1, .5, 1, 5, 10]),
                'target_value' => fake()->randomFloat(min: 0.1, max: 30),
                'single' => false,
                'overflow' => true,
            ];
        }
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
