<?php

namespace App\Listeners;

use App\Events\TrackerUpdated;
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
    }
}
