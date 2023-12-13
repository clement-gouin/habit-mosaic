<?php

namespace Tests\Feature\Controllers\Api;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DayDataControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_today_data(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);

        $tracker = Tracker::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user)
            ->getJson(route('day.data'))
            ->assertSuccessful()
            ->assertJsonFragment(['date' => Carbon::today()->timestamp])
            ->assertJsonFragment(['id' => $tracker->id]);
    }

    /** @test */
    public function it_shows_another_day_data(): void
    {
        $date = Carbon::today()->subDay();

        $this->actingAs(User::factory()->create())
            ->getJson(route('day.data', ['date' => $date->toIso8601String()]))
            ->assertSuccessful()
            ->assertJsonFragment(['date' => $date->timestamp]);
    }

    /** @test */
    public function it_shows_invalid_day_data_with_today_fallback(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson(route('day.data', ['date' => 'invalid']))
            ->assertSuccessful()
            ->assertJsonFragment(['date' => Carbon::today()->timestamp]);
    }
}
