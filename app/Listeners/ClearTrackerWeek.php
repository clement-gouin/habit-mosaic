<?php

namespace App\Listeners;

use App\Events\TrackerUpdated;
use App\Events\CategoryUpdated;
use App\Events\DataPointUpdated;
use App\Services\Mosaic\TrackerMosaicService;

class ClearTrackerWeek
{
    public function __construct(
        protected TrackerMosaicService $mosaicService,
    ) {
    }

    public function handle(DataPointUpdated $event): void
    {
        $this->mosaicService->clearData($event->dataPoint->tracker, $event->dataPoint->date);
    }
}
