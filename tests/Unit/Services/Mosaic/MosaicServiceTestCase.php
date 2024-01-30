<?php

namespace Tests\Unit\Services\Mosaic;

use App\Models\DataPoint;
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
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_gets_all_mosaic_data_with_data(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_gets_all_mosaic_data_with_cache(): void
    {
        $this->markTestSkipped('TODO');
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
    public function it_can_clear_data_for_week(): void
    {
        $target = $this->createTarget();

        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            $this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $this->service->clearData($target, $lastWeek);

        $this->assertFalse(
            Cache::has($this->getCacheRootKey($target).'.'.$lastWeek->year.'.'.$lastWeek->week)
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
    }

    /** @test */
    public function it_can_get_statistics_empty(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_can_get_statistics_with_data(): void
    {
        $this->markTestSkipped('TODO');
    }

    /** @test */
    public function it_can_get_statistics_with_cache_expired(): void
    {
        $this->markTestSkipped('TODO');
    }
}
