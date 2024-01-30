<?php

namespace Tests\Unit\Listeners;

use Tests\TestCase;
use App\Models\Tracker;
use Mockery\MockInterface;
use App\Events\TrackerScoreUpdated;
use App\Listeners\WipeTrackerMosaic;
use Illuminate\Support\Facades\Event;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class WipeTrackerMosaicTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_listens_to_events(): void
    {
        Event::assertListening(TrackerScoreUpdated::class, WipeTrackerMosaic::class);
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

        $listener = new WipeTrackerMosaic($mosaicService);

        $listener->handle(new TrackerScoreUpdated($tracker));
    }
}
