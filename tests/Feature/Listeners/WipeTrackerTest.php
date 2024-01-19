<?php

namespace Tests\Feature\Listeners;

use App\Events\TrackerUpdated;
use App\Listeners\WipeTracker;
use App\Models\Tracker;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

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
