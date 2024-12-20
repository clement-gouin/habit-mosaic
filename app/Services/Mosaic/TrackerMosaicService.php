<?php

namespace App\Services\Mosaic;

use App\Models\Tracker;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * @extends MosaicService<Tracker>
 */
class TrackerMosaicService extends MosaicService
{
    /** @param Tracker $value */
    protected function computeWeekData($value, CarbonInterface $startDate): array
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
    protected static function getRootCacheKey($value): string
    {
        return 'mosaic.tracker.'.$value->id;
    }

    /** @param Tracker $value */
    public function getMaxDate($value): ?CarbonInterface
    {
        return new Carbon(strval($value->dataPoints()->min('date')));
    }
}
