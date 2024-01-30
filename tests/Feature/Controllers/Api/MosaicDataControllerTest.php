<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Category;
use App\Models\Tracker;
use App\Models\User;
use App\Services\Mosaic\CategoryMosaicService;
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MosaicDataControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_tracker_mosaic(): void
    {
        $tracker = Tracker::factory()->create();

        $this->getMock(TrackerMosaicService::class)
            ->expects('getMosaicData')
            ->with(self::modelArg($tracker), 7)
            ->andReturn([null, null, null, null, null, 1.0, 0.0]);

        $this->actingAs($tracker->user)
            ->getJson(route('mosaic.tracker', [$tracker->id, 'days' => 7]))
            ->assertSuccessful()
            ->assertJson([null, null, null, null, null, 1.0, 0.0]);
    }

    /** @test */
    public function it_shows_category_mosaic(): void
    {
        $category = Category::factory()->create();

        $this->getMock(CategoryMosaicService::class)
            ->expects('getMosaicData')
            ->with(self::modelArg($category), 7)
            ->andReturn([null, null, null, null, null, 1.0, 0.0]);

        $this->actingAs($category->user)
            ->getJson(route('mosaic.category', [$category->id, 'days' => 7]))
            ->assertSuccessful()
            ->assertJson([null, null, null, null, null, 1.0, 0.0]);
    }

    /** @test */
    public function it_shows_day_mosaic(): void
    {
        $user = User::factory()->create();

        $this->getMock(DayMosaicService::class)
            ->expects('getMosaicData')
            ->with(self::modelArg($user), 7)
            ->andReturn([null, null, null, null, null, 1.0, 0.0]);

        $this->actingAs($user)
            ->getJson(route('mosaic.day', ['days' => 7]))
            ->assertSuccessful()
            ->assertJson([null, null, null, null, null, 1.0, 0.0]);
    }
}
