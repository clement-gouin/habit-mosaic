<?php

namespace Tests\Feature\Controllers\Web;

use App\Models\Category;
use App\Models\Tracker;
use App\Models\User;
use App\Services\Mosaic\CategoryMosaicService;
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class DayViewControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_view(): void
    {
        $user = User::factory()->create();

        $this->mockMosaicServiceStatistics(DayMosaicService::class, $user);
        $this->mockMosaicServiceStatistics(CategoryMosaicService::class, times: 3);
        $this->mockMosaicServiceStatistics(TrackerMosaicService::class, times: 20);

        $categories = Category::factory(3)->create([
            'user_id' => $user->id,
        ]);

        Tracker::factory(20)->create([
            'category_id' => fake()->randomElement($categories)->id,
        ]);

        $this->actingAs($user)
            ->getJson(route('day'))
            ->assertSuccessful()
            ->assertViewIs('day_view')
            ->assertViewHas('date', Carbon::today()->format('Y-m-d'))
            ->assertViewHas('statistics')
            ->assertViewHas('categories')
            ->assertViewHas('trackers');
    }
}
