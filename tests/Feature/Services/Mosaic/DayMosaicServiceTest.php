<?php

namespace Tests\Feature\Services\Mosaic;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\DataPoint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\Mosaic\DayMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DayMosaicServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected DayMosaicService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[DayMosaicService::class];

        $fakeToday = Carbon::today()->startOfWeek()->addDay();

        $this->freezeTime();
        Carbon::setTestNow($fakeToday);
    }

    /** @test */
    public function it_gets_mosaic_data_empty(): void
    {
        $user = User::factory()->create();

        $data = $this->service->getMosaicData($user, 7);

        $this->assertEquals([null, null, null, null, null, 0.0, 0.0], $data);
    }

    /** @test */
    public function it_gets_mosaic_data_with_data(): void
    {
        $user = User::factory()->create();

        $tracker = Tracker::factory()->create([
            'user_id' => $user->id,
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

        $data = $this->service->getMosaicData($user, 14);

        $this->assertEquals(
            [null, null, null, null, null, 1, 0, 0, 0, 0, 0, 0, 2, 0],
            $data
        );

        $this->assertEquals(
            Carbon::today()->subWeeks(2),
            Cache::get('mosaic.day.' . $user->id . '.max')
        );
    }

    /** @test */
    public function it_gets_mosaic_data_with_data_with_cache(): void
    {
        $user = User::factory()->create();

        Cache::put('mosaic.day.' . $user->id . '.max', Carbon::today()->subWeeks(2));
        $today = Carbon::today();
        Cache::put(
            'mosaic.day.' . $user->id . '.' . $today->year . '.' . $today->week,
            [null, null, null, null, null, 1, 2]
        );
        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            'mosaic.day.' . $user->id . '.' . $lastWeek->year . '.' . $lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $data = $this->service->getMosaicData($user, 14);

        $this->assertEquals(
            [null, null, null, null, null, 1, 2, 1, 2, 3, 4, 5, 6, 7],
            $data
        );
    }

    /** @test */
    public function it_can_clear_data_for_week(): void
    {
        $user = User::factory()->create();

        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            'mosaic.day.' . $user->id . '.' . $lastWeek->year . '.' . $lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $this->service->clearData($user, $lastWeek);

        $this->assertFalse(
            Cache::has('mosaic.day.' . $user->id . '.' . $lastWeek->year . '.' . $lastWeek->week)
        );
    }

    /** @test */
    public function it_wipe_data(): void
    {
        $user = User::factory()->create();

        Cache::put('mosaic.day.' . $user->id . '.max', Carbon::today()->subWeeks(2));
        $today = Carbon::today();
        Cache::put(
            'mosaic.day.' . $user->id . '.' . $today->year . '.' . $today->week,
            [null, null, null, null, null, 1, 2]
        );
        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            'mosaic.day.' . $user->id . '.' . $lastWeek->year . '.' . $lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $this->service->wipeData($user);

        $this->assertFalse(
            Cache::has('mosaic.day.' . $user->id . '.max')
        );
        $this->assertFalse(
            Cache::has('mosaic.day.' . $user->id . '.' . $today->year . '.' . $today->week)
        );
        $this->assertFalse(
            Cache::has('mosaic.day.' . $user->id . '.' . $lastWeek->year . '.' . $lastWeek->week)
        );
    }
}
