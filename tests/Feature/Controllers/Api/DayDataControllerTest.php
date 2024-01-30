<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Category;
use App\Models\Tracker;
use App\Models\User;
use App\Services\Mosaic\CategoryMosaicService;
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

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

        $this->mockMosaicServiceStatistics(DayMosaicService::class, $user);
        $this->mockMosaicServiceStatistics(CategoryMosaicService::class, $category);
        $this->mockMosaicServiceStatistics(TrackerMosaicService::class, $tracker);

        $this->actingAs($user)
            ->getJson(route('day.data'))
            ->assertJsonFragment(['date' => Carbon::today()->format('Y-m-d')])
            ->assertSuccessful()
            ->assertJsonFragment(['date' => Carbon::today()->format('Y-m-d')])
            ->assertJsonFragment(['id' => $tracker->id]);
    }

    /** @test */
    public function it_shows_another_day_data(): void
    {
        $date = Carbon::today()->subDay();

        $user = User::factory()->create();

        $this->mockMosaicServiceStatistics(DayMosaicService::class, $user);

        $this->actingAs($user)
            ->getJson(route('day.data', ['date' => $date->toIso8601String()]))
            ->assertSuccessful()
            ->assertJsonFragment(['date' => $date->format('Y-m-d')]);
    }

    /** @test */
    public function it_shows_invalid_day_data_with_today_fallback(): void
    {
        $user = User::factory()->create();

        $this->mockMosaicServiceStatistics(DayMosaicService::class, $user);

        $this->actingAs($user)
            ->getJson(route('day.data', ['date' => 'invalid']))
            ->assertSuccessful()
            ->assertJsonFragment(['date' => Carbon::today()->format('Y-m-d')]);
    }
}
