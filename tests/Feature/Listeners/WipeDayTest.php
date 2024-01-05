<?php

namespace Tests\Feature\Listeners;

use Tests\TestCase;
use App\Models\Tracker;
use App\Models\Category;
use Mockery\MockInterface;
use App\Listeners\WipeDay;
use App\Events\TrackerUpdated;
use App\Events\CategoryUpdated;
use Illuminate\Support\Facades\Event;
use App\Services\Mosaic\DayMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class WipeDayTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_listens_to_events(): void
    {
        Event::assertListening(TrackerUpdated::class, WipeDay::class);
        Event::assertListening(CategoryUpdated::class, WipeDay::class);
    }

    /** @test */
    public function it_process_event_tracker(): void
    {
        $tracker = Tracker::factory()->create();

        /** @var DayMosaicService|MockInterface $mosaic */
        $mosaicService = $this->mock(DayMosaicService::class);
        $mosaicService
            ->expects('wipeData')
            ->with($tracker->user);

        $listener = new WipeDay($mosaicService);

        $listener->handle(new TrackerUpdated($tracker));
    }

    /** @test */
    public function it_process_event_category(): void
    {
        $category = Category::factory()->create();

        /** @var DayMosaicService|MockInterface $mosaic */
        $mosaicService = $this->mock(DayMosaicService::class);
        $mosaicService
            ->expects('wipeData')
            ->with($category->user);

        $listener = new WipeDay($mosaicService);

        $listener->handle(new CategoryUpdated($category));
    }
}
