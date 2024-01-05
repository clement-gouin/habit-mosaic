<?php

namespace App\Listeners;

use App\Events\TrackerUpdated;
use App\Events\CategoryUpdated;
use App\Services\Mosaic\TrackerMosaicService;

class WipeTracker
{
    public function __construct(
        protected TrackerMosaicService $mosaicService,
    ) {
    }

    public function handle(TrackerUpdated $event): void
    {
        $this->mosaicService->wipeData($event->tracker);

        if ($event->tracker->category !== null) {
            CategoryUpdated::dispatch($event->tracker->category);
        }
    }
}
