<?php

namespace App\Listeners;

use App\Interfaces\WithUser;
use App\Services\Mosaic\DayMosaicService;

class WipeDay
{
    public function __construct(
        protected DayMosaicService $mosaicService,
    ) {
    }

    public function handle(WithUser $event): void
    {
        $this->mosaicService->wipeData($event->getUser());
    }
}
