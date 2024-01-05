<?php

namespace App\Providers;

use App\Listeners\WipeDay;
use App\Listeners\WipeTracker;
use App\Events\TrackerUpdated;
use App\Listeners\WipeCategory;
use App\Events\CategoryUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CategoryUpdated::class => [
            WipeCategory::class,
            WipeDay::class,
        ],
        TrackerUpdated::class => [
            WipeTracker::class,
            WipeDay::class,
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
