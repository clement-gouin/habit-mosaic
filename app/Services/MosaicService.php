<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Support\Carbon;

class MosaicService
{
    public function getDayMosaicData(User $user, int $days): array
    {
        return $this->mapThroughTime(fn (Carbon $date) => $user->trackers->sum(fn (Tracker $tracker) => $tracker->getScoreAt($date)), $days);
    }

    public function getCategoryMosaicData(Category $category, int $days): array
    {
        return $this->mapThroughTime(fn (Carbon $date) => $category->trackers->sum(fn (Tracker $tracker) => $tracker->getScoreAt($date)), $days);
    }

    public function getTrackerMosaicData(Tracker $tracker, int $days): array
    {
        return $this->mapThroughTime(fn (Carbon $date) => $tracker->getScoreAt($date), $days);
    }

    protected function mapThroughTime(callable $callable, int $days): array
    {
        $today = Carbon::today();
        $endDate = $today->clone()->startOfWeek()->addWeek()->subDay();

        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $endDate->clone()->subDays($i);
            $data[] = $date->isAfter($today) ? null : $callable($date);
        }

        return $data;
    }
}
