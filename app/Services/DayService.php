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
}
