<?php

namespace Tests\Feature\Controllers\Web;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DashboardControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_view(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);

        $tracker = Tracker::factory()->create([
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($tracker->user)
            ->getJson(route('dashboard'))
            ->assertSuccessful()
            ->assertViewIs('dashboard');
    }
}
