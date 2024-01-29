<?php

namespace App\Listeners;

use App\Events\DataPointUpdated;
use App\Services\Mosaic\CategoryMosaicService;
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\TrackerMosaicService;

class ClearTrackerWeekMosaic
{
    public function __construct(
        protected TrackerMosaicService $mosaicService,
        protected CategoryMosaicService $catMosaicService,
        protected DayMosaicService $dayMosaicService,
    ) {
    }

    public function handle(DataPointUpdated $event): void
    {
        $this->mosaicService->clearData($event->dataPoint->tracker, $event->dataPoint->date);

        if ($event->dataPoint->tracker->category) {
            $this->catMosaicService->clearData($event->dataPoint->tracker->category, $event->dataPoint->date);
        }

        $this->dayMosaicService->clearData($event->dataPoint->tracker->user, $event->dataPoint->date);
    }
}