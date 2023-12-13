<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
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
            'icon' => fake()->boolean() ? fake()->randomElement(['shower', 'tv', 'faucet', 'blender', 'plug']) : null,
            'order' => fn () => Category::count() + 1,
        ];
    }
}
