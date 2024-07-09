<?php

namespace Tests\Unit\Services\Mosaic;

use App\Models\DataPoint;
use App\Objects\Statistics;
use App\Services\Mosaic\MosaicService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

abstract class MosaicServiceTestCase extends TestCase
{
    protected MosaicService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $fakeToday = Carbon::today()->startOfWeek()->addDay();

        Carbon::setTestNow($fakeToday);

        $this->service = $this->makeService();
    }

    abstract public function makeService(): MosaicService;

    abstract public function createTarget(): Model;

    abstract public function getCacheRootKey(Model $target): string;

    abstract public function attachDataPointsToTarget(Model $target, array $dataPoints): void;

    /** @test */
    public function it_gets_all_mosaic_data_empty(): void
    {
        $target = $this->createTarget();

        $data = $this->service->getAllMosaicData($target);

        $this->assertEquals([null, null, null, null, null, 0, 0], $data);
    }

    /** @test */
    public function it_gets_all_mosaic_data_with_data(): void
    {
        $target = $this->createTarget();

        $this->attachDataPointsToTarget($target, [
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 1,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subWeeks(2),
                'value' => 2,
            ]),
        ]);

        $data = $this->service->getAllMosaicData($target);

        $this->assertEquals(
            [null, null, null, null, null, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0],
            $data
        );

        $this->assertEquals(
            Carbon::today()->startOfWeek()->subWeeks(3),
            Cache::get($this->getCacheRootKey($target).'.max')
        );
    }

    /** @test */
    public function it_gets_all_mosaic_data_with_cache(): void
    {
        $target = $this->createTarget();

        $this->attachDataPointsToTarget($target, [
            DataPoint::factory()->make([
                'date' => Carbon::today()->subWeek(),
                'value' => 2,
            ]),
        ]);

        Cache::put($this->getCacheRootKey($target).'.max', Carbon::today()->subWeeks(2));
        $today = Carbon::today();
        Cache::put(
            $this->getCacheRootKey($target).'.'.$today->year.'.'.$today->week,
            [null, null, null, null, null, 1, 2]
        );
        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            $this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $data = $this->service->getAllMosaicData($target);

        $this->assertEquals(
            [null, null, null, null, null, 1, 2, 1, 2, 3, 4, 5, 6, 7],
            $data
        );
    }

    /** @test */
    public function it_gets_mosaic_data_empty(): void
    {
        $target = $this->createTarget();

        $data = $this->service->getMosaicData($target, 3);

        $this->assertEquals([null, null, null, null, null, 0.0, 0.0], $data);
    }

    /** @test */
    public function it_gets_mosaic_data_with_data(): void
    {
        $target = $this->createTarget();

        $this->attachDataPointsToTarget($target, [
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 1,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subWeek(),
                'value' => 2,
            ]),
        ]);

        $data = $this->service->getMosaicData($target, 12);

        $this->assertEquals(
            [null, null, null, null, null, 1, 0, 0, 0, 0, 0, 0, 2, 0],
            $data
        );

        $this->assertEquals(
            Carbon::today()->startOfWeek()->subWeeks(2),
            Cache::get($this->getCacheRootKey($target).'.max')
        );
    }

    /** @test */
    public function it_gets_mosaic_data_with_data_with_cache(): void
    {
        $target = $this->createTarget();

        Cache::put($this->getCacheRootKey($target).'.max', Carbon::today()->subWeeks(2));
        $today = Carbon::today();
        Cache::put(
            $this->getCacheRootKey($target).'.'.$today->year.'.'.$today->week,
            [null, null, null, null, null, 1, 2]
        );
        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            $this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $data = $this->service->getMosaicData($target, 12);

        $this->assertEquals(
            [null, null, null, null, null, 1, 2, 1, 2, 3, 4, 5, 6, 7],
            $data
        );
    }

    /** @test */
    public function it_gets_average_data_empty(): void
    {
        $target = $this->createTarget();

        $data = $this->service->getAverageData($target, 3);

        $this->assertEquals([0.0], $data);
    }

    /** @test */
    public function it_gets_average_data_first_week(): void
    {
        $target = $this->createTarget();

        $this->attachDataPointsToTarget($target, [
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 1,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subDay(),
                'value' => 2,
            ]),
        ]);

        $data = $this->service->getAverageData($target, 3);

        $this->assertEquals([1.5], $data);
    }

    /** @test */
    public function it_gets_average_data_middle_week(): void
    {
        $target = $this->createTarget();

        $maxDay = Carbon::today()->subWeek()->startOfWeek();

        Cache::put($this->getCacheRootKey($target).'.max', $maxDay);
        Cache::put(
            $this->getCacheRootKey($target).'.'.$maxDay->year.'.'.$maxDay->week.'.avg',
            2.5
        );

        $this->attachDataPointsToTarget($target, [
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 1,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subDay(),
                'value' => 2,
            ]),
        ]);

        $data = $this->service->getAverageData($target, 14);

        $this->assertEquals([2.0, 2.5], $data);
    }

    /** @test */
    public function it_gets_average_data_with_cache(): void
    {
        $target = $this->createTarget();

        $maxDay = Carbon::today()->subWeek()->startOfWeek();

        Cache::put($this->getCacheRootKey($target).'.max', $maxDay);
        Cache::put(
            $this->getCacheRootKey($target).'.'.$maxDay->year.'.'.$maxDay->week.'.avg',
            2.5
        );
        Cache::put(
            $this->getCacheRootKey($target).'.'.Carbon::today()->year.'.'.Carbon::today()->week.'.avg',
            2.0
        );

        $data = $this->service->getAverageData($target, 14);

        $this->assertEquals([2.0, 2.5], $data);
    }

    /** @test */
    public function it_can_clear_data_for_week(): void
    {
        $target = $this->createTarget();

        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            $this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );
        Cache::put(
            $this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week.'.avg',
            1.5
        );

        $this->service->clearData($target, $lastWeek);

        $this->assertFalse(
            Cache::has($this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week)
        );
        $this->assertFalse(
            Cache::has($this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week.'.avg')
        );
    }

    /** @test */
    public function it_wipes_data(): void
    {
        $target = $this->createTarget();

        Cache::put($this->getCacheRootKey($target).'.max', Carbon::today()->subWeeks(2));
        $today = Carbon::today();
        Cache::put(
            $this->getCacheRootKey($target).'.'.$today->year.'.'.$today->week,
            [null, null, null, null, null, 1, 2]
        );
        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            $this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );
        Cache::put(
            $this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week.'.avg',
            1.5
        );

        $this->service->wipeData($target);

        $this->assertFalse(
            Cache::has($this->getCacheRootKey($target).'.max')
        );
        $this->assertFalse(
            Cache::has($this->getCacheRootKey($target).'.'.$today->year.'.'.$today->week)
        );
        $this->assertFalse(
            Cache::has($this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week)
        );
        $this->assertFalse(
            Cache::has($this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week.'.avg')
        );
    }

    /** @test */
    public function it_can_get_statistics_empty(): void
    {
        $target = $this->createTarget();

        $statistics = $this->service->getStatistics($target);

        $this->assertEquals(2, $statistics->total);
        $this->assertEquals(0, $statistics->average);

        $this->assertEquals(
            $statistics->serialize(),
            Cache::get($this->getCacheRootKey($target).'.statistics')
        );
    }

    /** @test */
    public function it_can_get_statistics_with_data(): void
    {
        $target = $this->createTarget();

        $this->attachDataPointsToTarget($target, [
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 1,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subWeek(),
                'value' => 8,
            ]),
        ]);

        $statistics = $this->service->getStatistics($target);

        $this->assertEquals(9, $statistics->total);
        $this->assertEquals(1, $statistics->average);

        $this->assertEquals(
            $statistics->serialize(),
            Cache::get($this->getCacheRootKey($target).'.statistics')
        );
    }

    /** @test */
    public function it_can_get_statistics_with_cache(): void
    {
        $target = $this->createTarget();

        $this->attachDataPointsToTarget($target, [
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 1,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subWeek(),
                'value' => 8,
            ]),
        ]);

        $cachedStatistics = Statistics::fromDataCollection(collect([1, 2, 3]));

        Cache::put(
            $this->getCacheRootKey($target).'.statistics',
            $cachedStatistics->serialize()
        );

        $statistics = $this->service->getStatistics($target);

        $this->assertEquals(3, $statistics->total);
        $this->assertEquals(2, $statistics->average);

        $this->assertEquals(
            $cachedStatistics->serialize(),
            Cache::get($this->getCacheRootKey($target).'.statistics')
        );
    }

    /** @test */
    public function it_can_get_statistics_with_cache_expired(): void
    {
        $target = $this->createTarget();

        $this->attachDataPointsToTarget($target, [
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 1,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subWeek(),
                'value' => 8,
            ]),
        ]);

        Cache::put(
            $this->getCacheRootKey($target).'.statistics',
            Statistics::fromDataCollection(collect([1, 2, 3]))->serialize(),
            Carbon::today()->subDay(),
        );

        $statistics = $this->service->getStatistics($target);

        $this->assertEquals(9, $statistics->total);
        $this->assertEquals(1, $statistics->average);

        $this->assertEquals(
            $statistics->serialize(),
            Cache::get($this->getCacheRootKey($target).'.statistics')
        );
    }
}
