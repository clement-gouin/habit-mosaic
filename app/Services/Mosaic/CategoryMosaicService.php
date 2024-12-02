<?php

namespace App\Services\Mosaic;

use App\Models\Category;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * @extends MosaicService<Category>
 */
class CategoryMosaicService extends MosaicService
{
    public function __construct(protected TrackerMosaicService $trackerMosaicService) {}

    /** @param Category $value */
    protected function computeWeekData($value, CarbonInterface $startDate): array
    {
        return $this->trackerMosaicService->getCollectionWeekData($value->trackers, $startDate);
    }

    /** @param Category $value */
    protected static function getRootCacheKey($value): string
    {
        return 'mosaic.category.'.$value->id;
    }

    /** @param Category $value */
    public function getMaxDate($value): ?CarbonInterface
    {
        return new Carbon(strval($value->dataPoints()->min('date')));
    }
}
