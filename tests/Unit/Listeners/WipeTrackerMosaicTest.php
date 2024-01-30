<?php

namespace Tests\Unit\Listeners;

use App\Events\TrackerScoreUpdated;
use App\Listeners\WipeTrackerMosaic;
use App\Models\Tracker;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

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
