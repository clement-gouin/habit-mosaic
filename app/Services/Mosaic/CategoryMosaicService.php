<?php

namespace App\Services\Mosaic;

use App\Models\Tracker;
use App\Models\Category;
use App\Events\CategoryWiped;
use Illuminate\Support\Carbon;

/**
 * @extends AbstractMosaicService<Category>
 */
class CategoryMosaicService extends AbstractMosaicService
{
    public function __construct(protected TrackerMosaicService $trackerMosaicService)
    {
    }

    /** @param Category $value */
    protected function computeWeekData($value, Carbon $startDate): array
    {
        return $this->trackerMosaicService->getCollectionWeekData($value->trackers, $startDate);
    }

    /** @param Category $value */
    protected function getRootCacheKey($value): string
    {
        return 'mosaic.category.' . $value->id;
    }

    /** @param Category $value */
    public function wipeData($value): void
    {
        parent::wipeData($value);

        CategoryWiped::dispatch($value);
    }
}
