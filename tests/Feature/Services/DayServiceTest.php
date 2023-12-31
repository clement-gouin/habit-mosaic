<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use App\Models\DataPoint;
use App\Services\DayService;
use Illuminate\Support\Carbon;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DayServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected DayService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[DayService::class];
    }

    /** @test */
    public function it_computes_average_no_data(): void
    {
        $user = User::factory()->create();

        $this->assertEquals(0, $this->service->getAverage($user));
    }

    /** @test */
    public function it_computes_average(): void
    {
        $user = User::factory()->create();

        $tracker = Tracker::factory()->create([
            'user_id' => $user->id,
            'single' => false,
            'target_value' => 2,
            'target_score' => 1.5,
        ]);

        $tracker->dataPoints()->save(
            DataPoint::factory()->make([
                'date' => Carbon::createFromTimestamp(0),
                'value' => 2,
            ])
        );

        $this->assertEquals(1.5, $this->service->getAverage($user));
    }

    /** @test */
    public function it_cleans_empty_day_without_data(): void
    {
        $user = User::factory()->create();

        $this->assertEquals(0, $this->service->cleanEmptyDays($user));
    }

    /** @test */
    public function it_cleans_empty_day_without_empty_days(): void
    {
        $user = User::factory()->create();

        $tracker = Tracker::factory()->create([
            'user_id' => $user->id,
            'single' => false,
            'target_value' => 2,
            'target_score' => 1.5,
        ]);

        $tracker->dataPoints()->saveMany([
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 0,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::yesterday(),
                'value' => 2,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::createFromTimestamp(0),
                'value' => 0,
            ]),
        ]);

        $this->assertEquals(0, $this->service->cleanEmptyDays($user));

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::today(),
        ]);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::yesterday(),
        ]);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::createFromTimestamp(0),
        ]);
    }

    /** @test */
    public function it_cleans_empty_day(): void
    {
        $user = User::factory()->create();

        $tracker = Tracker::factory()->create([
            'user_id' => $user->id,
            'single' => false,
            'target_value' => 2,
            'target_score' => 1.5,
        ]);

        $tracker->dataPoints()->saveMany([
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 0,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::yesterday(),
                'value' => 0,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::createFromTimestamp(0),
                'value' => 0,
            ]),
        ]);

        $this->assertEquals(1, $this->service->cleanEmptyDays($user));

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::today(),
        ]);

        $this->assertDatabaseMissing('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::yesterday(),
        ]);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::createFromTimestamp(0),
        ]);
    }
}
