<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use App\Services\TableService;
use Illuminate\Support\Carbon;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DataPointResource;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TableServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected TableService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[TableService::class];
    }

    /** @test */
    public function it_gets_current_span_data(): void
    {
        $user = User::factory()->create();

        $span = fake()->randomNumber(nbDigits: 2, strict: true) + 2;

        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);

        $tracker = Tracker::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $result = $this->service->getTableData($user, 'today', $span);

        $this->assertEquals(Carbon::today()->format('Y-m-d'), $result['date']);
        $this->assertEquals($span, $result['days']);

        $this->assertInstanceOf(ResourceCollection::class, $result['categories']);
        $this->assertCount(1, $result['categories']);
        $this->assertInstanceOf(CategoryResource::class, $result['categories'][0]);
        $this->assertEquals($category->id, $result['categories'][0]->resource->id);
        $this->assertInstanceOf(ResourceCollection::class, $result['trackers']);
        $this->assertCount(1, $result['trackers']);
        $this->assertInstanceOf(TrackerResource::class, $result['trackers'][0]);
        $this->assertEquals($tracker->id, $result['trackers'][0]->resource->id);

        $today = Carbon::today()->format('Y-m-d');
        $this->assertArrayHasKey($today, $result['data']);
        $this->assertCount(1, $result['data'][$today]);
        $this->assertInstanceOf(DataPointResource::class, $result['data'][$today][0]);

        $maxDate = Carbon::today()->subDays($span - 1)->format('Y-m-d');
        $this->assertArrayHasKey($maxDate, $result['data']);
        $this->assertCount(1, $result['data'][$maxDate]);
        $this->assertInstanceOf(DataPointResource::class, $result['data'][$maxDate][0]);
    }

    /** @test */
    public function it_gets_another_day_data(): void
    {
        $user = User::factory()->create();

        $result = $this->service->getTableData($user, Carbon::yesterday()->format('Y-m-d'), 7);

        $this->assertEquals(Carbon::yesterday()->format('Y-m-d'), $result['date']);
    }

    /** @test */
    public function it_gets_current_span_data_on_parse_fail(): void
    {
        $user = User::factory()->create();

        $result = $this->service->getTableData($user, 'invalid', 7);

        $this->assertEquals(Carbon::today()->format('Y-m-d'), $result['date']);
    }
}
