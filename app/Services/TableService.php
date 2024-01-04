<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tracker;
use Illuminate\Support\Carbon;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DataPointResource;
use App\Http\Resources\TrackerFullResource;
use Carbon\Exceptions\InvalidFormatException;

class TableService
{
    public function getTableData(User $user, string $rawDate, int $days): array
    {
        try {
            $endDate = Carbon::parse($rawDate);
        } catch (InvalidFormatException) {
            $endDate = Carbon::today();
        }

        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $endDate->clone()->subDays($i);
            $data[$date->format('Y-m-d')] = $user->trackers->map(fn (Tracker $tracker) => $tracker->getDataPointAt($date));
        }

        return [
            'date' => $endDate->format('Y-m-d'),
            'days' => $days,
            'categories' => CategoryResource::collection($user->categories),
            'trackers' => TrackerResource::collection($user->trackers),
            'data' => $data,
        ];
    }
}
