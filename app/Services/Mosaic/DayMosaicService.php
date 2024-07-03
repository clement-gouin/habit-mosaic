<?php

namespace App\Services\Mosaic;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * @extends MosaicService<User>
 */
class DayMosaicService extends MosaicService
{
    public function __construct(protected TrackerMosaicService $trackerMosaicService) {}

    /** @param User $value */
    protected function computeWeekData($value, CarbonInterface $startDate): array
    {
        return $this->trackerMosaicService->getCollectionWeekData($value->trackers, $startDate);
    }

    /** @param User $value */
    protected static function getRootCacheKey($value): string
    {
        return 'mosaic.day.'.$value->id;
    }

    /** @param User $value */
    protected function getMaxDate($value): ?CarbonInterface
    {
        return new Carbon(strval($value->dataPoints()->min('date')));
    }
}
