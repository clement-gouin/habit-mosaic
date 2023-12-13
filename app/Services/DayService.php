<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;
use Carbon\Exceptions\InvalidFormatException;

class DayService
{
    public function getDayData(User $user, string $rawDate): array
    {
        try {
            $date = Carbon::parse($rawDate);
        } catch (InvalidFormatException) {
            $date = Carbon::today();
        }

        return [
            'date' => $date->timestamp,
            'categories' => CategoryResource::collection($user->categories),
            'trackers' => TrackerResource::collection($user->trackers),
        ];
    }
}
