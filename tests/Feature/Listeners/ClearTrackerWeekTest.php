<?php

namespace Tests\Feature\Listeners;

use Mockery;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
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
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use App\Services\Mosaic\CategoryMosaicService;
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

        /** @var TrackerMosaicService|MockInterface $trackerMosaicService */
        $trackerMosaicService = $this->mock(TrackerMosaicService::class);
        $trackerMosaicService
            ->expects('clearData')
            ->with(
                Mockery::on(fn (Tracker $arg) => $arg->id === $tracker->id),
                Mockery::on(fn (Carbon $arg) => $arg->is($dataPoint->date)),
            );

        /** @var CategoryMosaicService|MockInterface $catMosaicService */
        $catMosaicService = $this->mock(CategoryMosaicService::class);

        /** @var DayMosaicService|MockInterface $dayMosaicService */
        $dayMosaicService = $this->mock(DayMosaicService::class);
        $dayMosaicService
            ->expects('clearData')
            ->with(
                Mockery::on(fn (User $arg) => $arg->id === $tracker->user->id),
                Mockery::on(fn (Carbon $arg) => $arg->is($dataPoint->date)),
            );

        $listener = new ClearTrackerWeek(
            $trackerMosaicService,
            $catMosaicService,
            $dayMosaicService,
        );

        $listener->handle(new DataPointUpdated($dataPoint));
    }

    /** @test */
    public function it_process_event_with_category(): void
    {
        $category = Category::factory()->create();

        $tracker = Tracker::factory()->create([
            'category_id' => $category->id,
        ]);

        $dataPoint = DataPoint::factory()->create([
            'tracker_id' => $tracker->id,
            'date' => fake()->date,
        ]);

        /** @var TrackerMosaicService|MockInterface $trackerMosaicService */
        $trackerMosaicService = $this->mock(TrackerMosaicService::class);
        $trackerMosaicService
            ->expects('clearData')
            ->with(
                Mockery::on(fn (Tracker $arg) => $arg->id === $tracker->id),
                Mockery::on(fn (Carbon $arg) => $arg->is($dataPoint->date)),
            );

        /** @var CategoryMosaicService|MockInterface $catMosaicService */
        $catMosaicService = $this->mock(CategoryMosaicService::class);
        $catMosaicService
            ->expects('clearData')
            ->with(
                Mockery::on(fn (Category $arg) => $arg->id === $category->id),
                Mockery::on(fn (Carbon $arg) => $arg->is($dataPoint->date)),
            );

        /** @var DayMosaicService|MockInterface $dayMosaicService */
        $dayMosaicService = $this->mock(DayMosaicService::class);
        $dayMosaicService
            ->expects('clearData')
            ->with(
                Mockery::on(fn (User $arg) => $arg->id === $tracker->user->id),
                Mockery::on(fn (Carbon $arg) => $arg->is($dataPoint->date)),
            );

        $listener = new ClearTrackerWeek(
            $trackerMosaicService,
            $catMosaicService,
            $dayMosaicService,
        );

        $listener->handle(new DataPointUpdated($dataPoint));
    }
}
