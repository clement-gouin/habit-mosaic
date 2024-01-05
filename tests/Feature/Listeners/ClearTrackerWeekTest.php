<?php

namespace Tests\Feature\Listeners;

use Mockery;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Tracker;
use App\Models\Category;
use App\Models\DataPoint;
use Mockery\MockInterface;
use App\Listeners\WipeTracker;
use App\Events\TrackerUpdated;
use App\Events\CategoryUpdated;
use App\Events\DataPointUpdated;
use App\Listeners\ClearTrackerWeek;
use Illuminate\Support\Facades\Event;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ClearTrackerWeekTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_listens_to_events(): void
    {
        Event::assertListening(DataPointUpdated::class, ClearTrackerWeek::class);
    }

    /** @test */
    public function it_process_event(): void
    {
        $tracker = Tracker::factory()->create();

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => $tracker->id,
            'date' => fake()->date,
        ]);

        /** @var TrackerMosaicService|MockInterface $mosaic */
        $mosaicService = $this->mock(TrackerMosaicService::class);
        $mosaicService
            ->expects('clearData')
            ->with(
                Mockery::on(fn (Tracker $arg) => $arg->id === $tracker->id),
                Mockery::on(fn (Carbon $arg) => $arg->is($dataPoint->date)),
            );

        $listener = new ClearTrackerWeek($mosaicService);

        $listener->handle(new DataPointUpdated($dataPoint));
    }
}
