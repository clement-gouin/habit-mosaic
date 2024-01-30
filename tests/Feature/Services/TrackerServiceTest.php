<?php

namespace Tests\Feature\Services;

use App\Events\CategoryUpdated;
use App\Events\TrackerScoreUpdated;
use App\Models\Category;
use App\Models\Tracker;
use App\Services\TrackerService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

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
    public function it_update_tracker_without_change(): void
    {
        $tracker = Tracker::factory()->create();

        $this->service->update($tracker, $tracker->attributesToArray());

        Event::assertNotDispatched(TrackerScoreUpdated::class);
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
            TrackerScoreUpdated::class,
            fn (TrackerScoreUpdated $event) => $event->tracker->id === $tracker->id
        );
    }

    /** @test */
    public function it_update_tracker_with_target_change_with_category(): void
    {
        $category = Category::factory()->create();

        $tracker = Tracker::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->service->update($tracker, [
            ...$tracker->attributesToArray(),
            'target_score' => 15,
        ]);

        Event::assertDispatched(
            TrackerScoreUpdated::class,
            fn (TrackerScoreUpdated $event) => $event->tracker->id === $tracker->id
        );

        Event::assertDispatched(
            CategoryUpdated::class,
            fn (CategoryUpdated $event) => $event->category->id === $category->id
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
