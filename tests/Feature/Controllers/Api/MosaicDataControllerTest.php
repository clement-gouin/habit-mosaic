<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Category;
use App\Models\DataPoint;
use App\Models\Tracker;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MosaicDataControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_tracker_mosaic(): void
    {
        $fakeToday = Carbon::today()->startOfWeek()->addDay();

        Carbon::setTestNow($fakeToday);

        $tracker = Tracker::factory()->create([
            'value_step' => 1,
            'target_value' => 1,
            'single' => true,
            'target_score' => 1,
        ]);

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => $tracker->id,
            'date' => $fakeToday,
            'value' => 1,
        ]);

        $this->actingAs($tracker->user)
            ->getJson(route('mosaic.tracker', [$tracker->id, 'days' => 7]))
            ->assertSuccessful()
            ->assertJson([null, null, null, null, null, 1.0, 0.0]);
    }

    /** @test */
    public function it_shows_category_mosaic(): void
    {
        $fakeToday = Carbon::today()->startOfWeek()->addDay();

        Carbon::setTestNow($fakeToday);

        $category = Category::factory()->create();

        $tracker = Tracker::factory()->create([
            'category_id' => $category->id,
            'value_step' => 1,
            'target_value' => 1,
            'single' => true,
            'target_score' => 1,
        ]);

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => $tracker->id,
            'date' => $fakeToday,
            'value' => 1,
        ]);

        $this->actingAs($category->user)
            ->getJson(route('mosaic.category', [$category->id, 'days' => 7]))
            ->assertSuccessful()
            ->assertJson([null, null, null, null, null, 1.0, 0.0]);
    }

    /** @test */
    public function it_shows_day_mosaic(): void
    {
        $fakeToday = Carbon::today()->startOfWeek()->addDay();

        Carbon::setTestNow($fakeToday);

        $tracker = Tracker::factory()->create([
            'value_step' => 1,
            'target_value' => 1,
            'single' => true,
            'target_score' => 1,
        ]);

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => $tracker->id,
            'date' => $fakeToday,
            'value' => 1,
        ]);

        $this->actingAs($tracker->user)
            ->getJson(route('mosaic.day', ['days' => 7]))
            ->assertSuccessful()
            ->assertJson([null, null, null, null, null, 1.0, 0.0]);
    }
}
