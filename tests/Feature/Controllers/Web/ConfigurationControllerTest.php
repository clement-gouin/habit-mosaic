<?php

namespace Tests\Feature\Controllers\Web;

use App\Models\Category;
use App\Models\Tracker;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConfigurationControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_view(): void
    {
        $user = User::factory()->create();

        $categories = Category::factory(3)->create([
            'user_id' => $user->id,
        ]);

        Tracker::factory(20)->create([
            'user_id' => $user->id,
            'category_id' => fake()->randomElement($categories)->id,
        ]);

        $this->actingAs($user)
            ->getJson(route('configuration'))
            ->assertSuccessful()
            ->assertViewIs('configuration')
            ->assertViewHas('categories')
            ->assertViewHas('trackers');
    }
}
