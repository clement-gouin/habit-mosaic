<?php

namespace App\Listeners;

use App\Events\TrackerScoreUpdated;
use App\Services\Mosaic\TrackerMosaicService;

class WipeTrackerMosaic
{
    public function __construct(
        protected TrackerMosaicService $mosaicService,
    ) {
    }

    public function handle(TrackerScoreUpdated $event): void
    {
        $this->mosaicService->wipeData($event->tracker);
    }
}
