<?php

namespace App\Services\Mosaic;

use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * @extends AbstractMosaicService<User>
 */
class DayMosaicService extends AbstractMosaicService
{
    public function __construct(protected TrackerMosaicService $trackerMosaicService)
    {
    }

    /** @param User $value */
    protected function computeWeekData($value, Carbon $startDate): array
    {
        return $this->trackerMosaicService->getCollectionWeekData($value->trackers, $startDate);
    }

    /** @param User $value */
    protected function getRootCacheKey($value): string
    {
        return 'mosaic.day.'.$value->id;
    }
}
