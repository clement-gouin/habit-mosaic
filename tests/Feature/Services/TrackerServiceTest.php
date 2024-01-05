<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\Tracker;
use App\Models\Category;
use App\Models\DataPoint;
use Illuminate\Support\Carbon;
use App\Events\TrackerUpdated;
use App\Events\CategoryUpdated;
use App\Services\TrackerService;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrackerServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected TrackerService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app[TrackerService::class];
    }

    /** @test */
    public function it_updates_average_for_tracker_without_data(): void
    {
        $tracker = Tracker::factory()->create();

        $this->service->updateAverage($tracker);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::createFromTimestamp(0),
            'value' => 0,
        ]);
    }

    /** @test */
    public function it_updates_average_for_tracker_with_data(): void
    {
        $tracker = Tracker::factory()->create();

        $tracker->dataPoints()->saveMany([
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 1,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subDay(),
                'value' => 1.5,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subDays(2),
                'value' => 2,
            ]),
        ]);

        $this->service->updateAverage($tracker);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::createFromTimestamp(0),
            'value' => 1.5,
        ]);
    }

    /** @test */
    public function it_updates_average_for_tracker_with_data_and_average(): void
    {
        $tracker = Tracker::factory()->create();

        $tracker->dataPoints()->saveMany([
            DataPoint::factory()->make([
                'date' => Carbon::today(),
                'value' => 1,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subDay(),
                'value' => 1.5,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::today()->subDays(2),
                'value' => 2,
            ]),
            DataPoint::factory()->make([
                'date' => Carbon::createFromTimestamp(0),
                'value' => 1.5,
            ]),
        ]);

        $this->service->updateAverage($tracker);

        $this->assertDatabaseHas('data_points', [
            'tracker_id' => $tracker->id,
            'date' => Carbon::createFromTimestamp(0),
            'value' => 1.5,
        ]);
    }

    /** @test */
    public function it_update_tracker_without_change(): void
    {
        $tracker = Tracker::factory()->create();

        $this->service->update($tracker, $tracker->attributesToArray());

        Event::assertNotDispatched(TrackerUpdated::class);
    }

    /** @test */
    public function it_update_tracker_with_target_change(): void
    {
        $tracker = Tracker::factory()->create();

        $this->service->update($tracker, [
            ...$tracker->attributesToArray(),
            'target_score' => 15,
        ]);

        Event::assertDispatched(
            TrackerUpdated::class,
            fn (TrackerUpdated $event) => $event->tracker->id === $tracker->id
        );
    }

    /** @test */
    public function it_update_tracker_with_category_change(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $tracker = Tracker::factory()->create([
            'category_id' => $category1->id,
        ]);

        $this->service->update($tracker, [
            ...$tracker->attributesToArray(),
            'category_id' => $category2->id,
        ]);

        Event::assertDispatched(
            CategoryUpdated::class,
            fn (CategoryUpdated $event) => $event->category->id === $category1->id
        );

        Event::assertDispatched(
            CategoryUpdated::class,
            fn (CategoryUpdated $event) => $event->category->id === $category2->id
        );
    }

    /** @test */
    public function it_update_tracker_adds_category(): void
    {
        $category = Category::factory()->create();

        $tracker = Tracker::factory()->create([
            'category_id' => null,
        ]);

        $this->service->update($tracker, [
            ...$tracker->attributesToArray(),
            'category_id' => $category->id,
        ]);

        Event::assertDispatched(
            CategoryUpdated::class,
            fn (CategoryUpdated $event) => $event->category->id === $category->id
        );
    }

    /** @test */
    public function it_update_tracker_removes_category(): void
    {
        $category = Category::factory()->create();

        $tracker = Tracker::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->service->update($tracker, [
            ...$tracker->attributesToArray(),
            'category_id' => null,
        ]);

        Event::assertDispatched(
            CategoryUpdated::class,
            fn (CategoryUpdated $event) => $event->category->id === $category->id
        );
    }
}
