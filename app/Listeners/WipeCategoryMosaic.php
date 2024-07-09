<?php

namespace App\Listeners;

use App\Events\CategoryEvent;
use App\Services\Mosaic\CategoryMosaicService;

class WipeCategoryMosaic
{
    public function __construct(protected CategoryMosaicService $mosaicService) {}

    public function handle(CategoryEvent $event): void
    {
        $this->mosaicService->wipeData($event->category);
    }
}
