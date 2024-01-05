<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
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
    public function it_gets_today_data(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create([
            'user_id' => $user->id,
        ]);

        $tracker = Tracker::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $result = $this->service->getDayData($user, 'today');

        $this->assertEquals(Carbon::today()->format('Y-m-d'), $result['date']);
        $this->assertInstanceOf(ResourceCollection::class, $result['categories']);
        $this->assertCount(1, $result['categories']);
        $this->assertInstanceOf(CategoryResource::class, $result['categories'][0]);
        $this->assertEquals($category->id, $result['categories'][0]->resource->id);
        $this->assertInstanceOf(ResourceCollection::class, $result['trackers']);
        $this->assertCount(1, $result['trackers']);
        $this->assertInstanceOf(TrackerResource::class, $result['trackers'][0]);
        $this->assertEquals($tracker->id, $result['trackers'][0]->resource->id);
    }

    /** @test */
    public function it_gets_another_day_data(): void
    {
        $user = User::factory()->create();

        $result = $this->service->getDayData($user, Carbon::yesterday()->format('Y-m-d'));

        $this->assertEquals(Carbon::yesterday()->format('Y-m-d'), $result['date']);
    }

    /** @test */
    public function it_gets_today_data_on_parse_fail(): void
    {
        $user = User::factory()->create();

        $result = $this->service->getDayData($user, 'abcdef');

        $this->assertEquals(Carbon::today()->format('Y-m-d'), $result['date']);
    }
}
