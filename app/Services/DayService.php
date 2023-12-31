<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tracker;
use Illuminate\Support\Carbon;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TrackerFullResource;
use Carbon\Exceptions\InvalidFormatException;

class DayService
{
    public function getAverage(User $user): float
    {
        return $user->trackers->sum(fn (Tracker $tracker) => $tracker->getAverageScore());
    }

    public function cleanEmptyDays(User $user): int
    {
        $date = $this->getStartDate($user);
        $today = Carbon::today();
        $count = 0;

        while ($date->notEqualTo($today)) {
            if ($this->isEmptyDay($user, $date)) {
                $this->cleanDay($user, $date);
                $count += 1;
            }
            $date->addDay();
        }

        return $count;
    }

    protected function isEmptyDay(User $user, Carbon $date): bool
    {
        return $user->dataPoints()
            ->where('date', '=', $date)
            ->where('value', '!=', 0)
            ->count() === 0;
    }

    protected function cleanDay(User $user, Carbon $date): void
    {
        $user->dataPoints()
            ->where('date', '=', $date)
            ->delete();
    }

    protected function getStartDate(User $user): Carbon
    {
        /** @var string $raw */
        $raw = $user->dataPoints()
            ->where('date', '!=', Carbon::createFromTimestamp(0))
            ->min('date');

        return $raw ? Carbon::parse($raw) : Carbon::today();
    }
}
