<?php

namespace Tests\Feature\Services\Mosaic;

use App\Models\Category;
use App\Models\DataPoint;
use App\Models\Tracker;
use App\Services\Mosaic\CategoryMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategoryMosaicServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected CategoryMosaicService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[CategoryMosaicService::class];

        $fakeToday = Carbon::today()->startOfWeek()->addDay();

        $this->freezeTime();
        Carbon::setTestNow($fakeToday);
    }

    /** @test */
    public function it_gets_mosaic_data_empty(): void
    {
        $category = Category::factory()->create();

        $data = $this->service->getMosaicData($category, 7);

        $this->assertEquals([null, null, null, null, null, 0.0, 0.0], $data);
    }

    /** @test */
    public function it_gets_mosaic_data_with_data(): void
    {
        $category = Category::factory()->create();

        $tracker = Tracker::factory()->create([
            'category_id' => $category->id,
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

        $data = $this->service->getMosaicData($category, 14);

        $this->assertEquals(
            [null, null, null, null, null, 1, 0, 0, 0, 0, 0, 0, 2, 0],
            $data
        );

        $this->assertEquals(
            Carbon::today()->startOfWeek()->subWeeks(2),
            Cache::get('mosaic.category.'.$category->id.'.max')
        );
    }

    /** @test */
    public function it_gets_mosaic_data_with_data_with_cache(): void
    {
        $category = Category::factory()->create();

        Cache::put('mosaic.category.'.$category->id.'.max', Carbon::today()->subWeeks(2));
        $today = Carbon::today();
        Cache::put(
            'mosaic.category.'.$category->id.'.'.$today->year.'.'.$today->week,
            [null, null, null, null, null, 1, 2]
        );
        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            'mosaic.category.'.$category->id.'.'.$lastWeek->year.'.'.$lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $data = $this->service->getMosaicData($category, 14);

        $this->assertEquals(
            [null, null, null, null, null, 1, 2, 1, 2, 3, 4, 5, 6, 7],
            $data
        );
    }

    /** @test */
    public function it_can_clear_data_for_week(): void
    {
        $category = Category::factory()->create();

        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            'mosaic.category.'.$category->id.'.'.$lastWeek->year.'.'.$lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $this->service->clearData($category, $lastWeek);

        $this->assertFalse(
            Cache::has('mosaic.category.'.$category->id.'.'.$lastWeek->year.'.'.$lastWeek->week)
        );
    }

    /** @test */
    public function it_wipe_data(): void
    {
        $category = Category::factory()->create();

        Cache::put('mosaic.category.'.$category->id.'.max', Carbon::today()->subWeeks(2));
        $today = Carbon::today();
        Cache::put(
            'mosaic.category.'.$category->id.'.'.$today->year.'.'.$today->week,
            [null, null, null, null, null, 1, 2]
        );
        $lastWeek = Carbon::today()->subWeek();
        Cache::put(
            'mosaic.category.'.$category->id.'.'.$lastWeek->year.'.'.$lastWeek->week,
            [1, 2, 3, 4, 5, 6, 7]
        );

        $this->service->wipeData($category);

        $this->assertFalse(
            Cache::has('mosaic.category.'.$category->id.'.max')
        );
        $this->assertFalse(
            Cache::has('mosaic.category.'.$category->id.'.'.$today->year.'.'.$today->week)
        );
        $this->assertFalse(
            Cache::has('mosaic.category.'.$category->id.'.'.$lastWeek->year.'.'.$lastWeek->week)
        );
    }
}
