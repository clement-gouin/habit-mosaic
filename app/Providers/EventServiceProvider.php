<?php

namespace App\Providers;

use App\Events\CategoryUpdated;
use App\Events\DataPointUpdated;
use App\Events\TrackerScoreUpdated;
use App\Listeners\ClearTrackerWeekMosaic;
use App\Listeners\WipeCategoryMosaic;
use App\Listeners\WipeDayMosaic;
use App\Listeners\WipeTrackerMosaic;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        DataPointUpdated::class => [
            ClearTrackerWeekMosaic::class,
        ],
        TrackerScoreUpdated::class => [
            WipeTrackerMosaic::class,
            WipeDayMosaic::class,
        ],
        CategoryUpdated::class => [
            WipeCategoryMosaic::class,
            WipeDayMosaic::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
