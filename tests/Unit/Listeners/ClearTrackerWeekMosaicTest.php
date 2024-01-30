<?php

namespace Tests\Unit\Listeners;

use App\Events\DataPointUpdated;
use App\Listeners\ClearTrackerWeekMosaic;
use App\Models\Category;
use App\Models\DataPoint;
use App\Models\Tracker;
use App\Models\User;
use App\Services\Mosaic\CategoryMosaicService;
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ClearTrackerWeekMosaicTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_listens_to_events(): void
    {
        Event::assertListening(DataPointUpdated::class, ClearTrackerWeekMosaic::class);
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

        $listener = new ClearTrackerWeekMosaic(
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

        $listener = new ClearTrackerWeekMosaic(
            $trackerMosaicService,
            $catMosaicService,
            $dayMosaicService,
        );

        $listener->handle(new DataPointUpdated($dataPoint));
    }
}
