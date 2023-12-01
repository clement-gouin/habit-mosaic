<?php

namespace Database\Factories;

use App\Models\Tracker;
use App\Models\DataPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataPoint>
 */
class DataPointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tracker_id' => Tracker::factory(),
            'date' => fake()->dateTimeBetween('-10 days'),
            'value' => fake()->randomFloat(max:30),
        ];
    }
}
