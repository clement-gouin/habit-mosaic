<?php

namespace App\Listeners;

use App\Events\CategoryWiped;
use App\Services\Mosaic\DayMosaicService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PropagateCategoryWipe
{
    public function __construct(protected DayMosaicService $dayMosaicService)
    {
    }

    public function handle(CategoryWiped $event): void
    {
        $this->dayMosaicService->wipeData($event->category->user);
    }
}
