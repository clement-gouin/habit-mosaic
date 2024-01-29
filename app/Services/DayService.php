<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

class DayService
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
