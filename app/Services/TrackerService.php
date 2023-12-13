<?php

namespace App\Services;

use App\Models\Tracker;
use App\Models\DataPoint;

class TrackerService
{
    public function updateAverage(Tracker $tracker): void
    {
        /** @var DataPoint $average */
        $average = $tracker->getAverageDataPoint();

        $average->update([
            'value' => $tracker->dataPoints()->where('date', '!=', $average->date)->pluck('value')->average() ?? 0,
        ]);
    }
}
