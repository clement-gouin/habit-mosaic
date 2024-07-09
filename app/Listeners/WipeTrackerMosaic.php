<?php

namespace App\Listeners;

use App\Events\TrackerEvent;
use App\Services\Mosaic\TrackerMosaicService;

class WipeTrackerMosaic
{
    public function __construct(
        protected TrackerMosaicService $mosaicService,
    ) {}

    public function handle(TrackerEvent $event): void
    {
        $this->mosaicService->wipeData($event->tracker);
    }
}
