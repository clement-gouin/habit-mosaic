<?php

namespace App\Services\Mosaic;

use App\Models\Tracker;
use Illuminate\Support\Carbon;

/**
 * @extends AbstractMosaicService<Tracker>
 */
class TrackerMosaicService extends AbstractMosaicService
{
    /** @param Tracker $value */
    protected function computeWeekData($value, Carbon $startDate): array
    {
        $today = Carbon::today();
        $endDate = $startDate->clone()->startOfWeek()->addWeek()->subDay();
        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $endDate->clone()->subDays($i);
            $data[] = $date->isAfter($today) ? null : $value->getScoreAt($date);
        }
        return $data;
    }

    /** @param Tracker $value */
    protected function getRootCacheKey($value): string
    {
        return 'mosaic.tracker.' . $value->id;
    }
}
