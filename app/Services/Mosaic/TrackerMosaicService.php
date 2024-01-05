<?php

namespace App\Services\Mosaic;

use Cache;
use App\Models\Tracker;
use Illuminate\Support\Carbon;

/**
 * @extends AbstractMosaicService<Tracker>
 */
class TrackerMosaicService extends AbstractMosaicService
{
    public function __construct(
        protected CategoryMosaicService $catMosaicService,
        protected DayMosaicService $dayMosaicService
    ) {
    }

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

    /**
     * @param Tracker $value
     */
    public function clearData($value, Carbon $date): void
    {
        parent::clearData($value, $date);

        if ($value->category) {
            $this->catMosaicService->clearData($value->category, $date);
        }

        $this->dayMosaicService->clearData($value->user, $date);
    }
}
