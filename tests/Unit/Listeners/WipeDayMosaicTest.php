<?php

namespace Tests\Unit\Listeners;

use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use App\Events\TrackerDeleted;
use App\Events\TrackerScoreUpdated;
use App\Listeners\WipeDayMosaic;
use App\Models\Category;
use App\Models\Tracker;
use App\Services\Mosaic\DayMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

class WipeDayMosaicTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_listens_to_events(): void
    {
        Event::assertListening(TrackerScoreUpdated::class, WipeDayMosaic::class);
        Event::assertListening(TrackerDeleted::class, WipeDayMosaic::class);
        Event::assertListening(CategoryUpdated::class, WipeDayMosaic::class);
        Event::assertListening(CategoryDeleted::class, WipeDayMosaic::class);
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

        $listener = new WipeDayMosaic($mosaicService);

        $listener->handle(new TrackerScoreUpdated($tracker));
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

        $listener = new WipeDayMosaic($mosaicService);

        $listener->handle(new CategoryUpdated($category));
    }
}
