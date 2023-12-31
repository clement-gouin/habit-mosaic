<?php

namespace Tests\Feature\Listeners;

use Tests\TestCase;
use App\Models\Tracker;
use App\Models\Category;
use Mockery\MockInterface;
use App\Listeners\WipeTracker;
use App\Events\TrackerUpdated;
use App\Events\CategoryUpdated;
use Illuminate\Support\Facades\Event;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class WipeTrackerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_listens_to_events(): void
    {
        Event::assertListening(TrackerUpdated::class, WipeTracker::class);
    }

    /** @test */
    public function it_process_event(): void
    {
        $tracker = Tracker::factory()->create();

        /** @var TrackerMosaicService|MockInterface $mosaic */
        $mosaicService = $this->mock(TrackerMosaicService::class);
        $mosaicService
            ->expects('wipeData')
            ->with($tracker);

        $listener = new WipeTracker($mosaicService);

        $listener->handle(new TrackerUpdated($tracker));
    }
}
