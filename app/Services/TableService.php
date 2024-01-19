<?php

namespace App\Services;

use App\Http\Resources\DataPointResource;
use App\Models\Tracker;
use App\Models\User;
use Illuminate\Support\Carbon;

class TableService
{
    public function __construct(protected DayService $dayService)
    {
    }

    public function getTableData(User $user, Carbon $endDate, int $days): array
    {
        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $endDate->clone()->subDays($i);
            $data[$date->format('Y-m-d')] = $user->trackers->map(
                fn (Tracker $tracker) => DataPointResource::make($tracker->getDataPointAt($date))
            );
        }

        return $data;
    }
}
