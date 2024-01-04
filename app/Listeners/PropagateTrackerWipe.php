<?php

namespace App\Listeners;

use App\Events\TrackerWiped;
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\CategoryMosaicService;

class PropagateTrackerWipe
{
    public function __construct(
        protected CategoryMosaicService $catMosaicService,
        protected DayMosaicService $dayMosaicService
    ) {
    }

    public function handle(TrackerWiped $event): void
    {
        if ($event->tracker->category) {
            $this->catMosaicService->wipeData($event->tracker->category);
        }

        $this->dayMosaicService->wipeData($event->tracker->user);
    }
}
