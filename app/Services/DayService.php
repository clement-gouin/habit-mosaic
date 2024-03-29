<?php

namespace App\Services;

use App\Models\DataPoint;
use App\Models\User;
use Illuminate\Support\Carbon;

class DayService extends Service
{
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
        $ids = $user->dataPoints()
            ->where('date', '=', $date)
            ->pluck('id');

        DataPoint::whereIn('id', $ids)
            ->delete();
    }

    protected function getStartDate(User $user): Carbon
    {
        /** @var string $raw */
        $raw = $user->dataPoints()
            ->min('date');

        return $raw ? Carbon::parse($raw) : Carbon::today();
    }
}
