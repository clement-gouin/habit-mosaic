<?php

namespace App\Listeners;

use App\Events\CategoryUpdated;
use App\Services\Mosaic\CategoryMosaicService;

class WipeCategoryMosaic
{
    public function __construct(protected CategoryMosaicService $mosaicService)
    {
    }

    public function handle(CategoryUpdated $event): void
    {
        $this->mosaicService->wipeData($event->category);
    }
}
