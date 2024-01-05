<?php

namespace Tests\Feature\Services\Mosaic;

use Tests\TestCase;
use App\Models\Tracker;
use App\Models\DataPoint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrackerMosaicServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected TrackerMosaicService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[TrackerMosaicService::class];

        $fakeToday = Carbon::today()->startOfWeek()->addDay();

        $this->freezeTime();
        Carbon::setTestNow($fakeToday);
    }

    /** @test */
    public function it_gets_mosaic_data_empty(): void
    {
        $tracker = Tracker::factory()->create();

        $data = $this->service->getMosaicData($tracker, 7);

        $this->assertEquals([null, null, null, null, null, 0.0, 0.0], $data);
    }

    /** @test */
    public function it_gets_mosaic_data_with_data(): void
    {
        $tracker = Tracker::factory()->create([
            'value_step' => 1,
            'target_value' => 1,
            'single' => true,
            'target_score' => 1,
            'overflow' => true,
        ]);

        $tracker->dataPoints()->saveMany([
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 1,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subWeek(),
                'value' => 2,
            ]),
        ]);

        $data = $this->service->getMosaicData($tracker, 14);

        $this->assertEquals(
            [null, null, null, null, null, 1, 0, 0, 0, 0, 0, 0, 2, 0],
            $data
        );

        $this->assertEquals(
            Carbon::today()->subWeeks(2),
            Cache::get('mosaic.tracker.' . $tracker->id . '.max')
        );
    }

    /** @test */
    public function it_gets_mosaic_data_with_data_with_cache(): void
    {
        $tracker = Tracker::factory()->create();

        Cache::put('mosaic.tracker.' . $tracker->id . '.max', Carbon::today()->subWeeks(2));
        $today = Carbon::today();
        Cache::put(
            'mosaic.tracker.' . $tracker->id . '.' . $today->year . '.' . $today->week,
            [null, null, null, null, null, 1, 2]
        );
        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            'mosaic.tracker.' . $tracker->id . '.' . $lastWeek->year . '.' . $lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $data = $this->service->getMosaicData($tracker, 14);

        $this->assertEquals(
            [null, null, null, null, null, 1, 2, 1, 2, 3, 4, 5, 6, 7],
            $data
        );
    }

    /** @test */
    public function it_can_clear_data_for_week(): void
    {
        $tracker = Tracker::factory()->create();

        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            'mosaic.tracker.' . $tracker->id . '.' . $lastWeek->year . '.' . $lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $this->service->clearData($tracker, $lastWeek);

        $this->assertFalse(
            Cache::has('mosaic.tracker.' . $tracker->id . '.' . $lastWeek->year . '.' . $lastWeek->week)
        );
    }

    /** @test */
    public function it_wipe_data(): void
    {
        $tracker = Tracker::factory()->create();

        Cache::put('mosaic.tracker.' . $tracker->id . '.max', Carbon::today()->subWeeks(2));
        $today = Carbon::today();
        Cache::put(
            'mosaic.tracker.' . $tracker->id . '.' . $today->year . '.' . $today->week,
            [null, null, null, null, null, 1, 2]
        );
        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            'mosaic.tracker.' . $tracker->id . '.' . $lastWeek->year . '.' . $lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $this->service->wipeData($tracker);

        $this->assertFalse(
            Cache::has('mosaic.tracker.' . $tracker->id . '.max')
        );
        $this->assertFalse(
            Cache::has('mosaic.tracker.' . $tracker->id . '.' . $today->year . '.' . $today->week)
        );
        $this->assertFalse(
            Cache::has('mosaic.tracker.' . $tracker->id . '.' . $lastWeek->year . '.' . $lastWeek->week)
        );
    }
}
