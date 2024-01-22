<?php

namespace App\Services;

use App\Events\CategoryUpdated;
use App\Events\TrackerUpdated;
use App\Models\DataPoint;
use App\Models\Tracker;

class TrackerService
{
    public function update(Tracker $tracker, array $attributes): void
    {
        $categoryChange = ($attributes['category_id'] ?? null) !== $tracker->category_id;
        $targetChange = $attributes['target_value'] !== $tracker->target_value ||
            $attributes['target_score'] !== $tracker->target_score;

        if ($categoryChange && $tracker->category) {
            CategoryUpdated::dispatch($tracker->category);
        }

        $tracker->fill($attributes)->save();

        $tracker = $tracker->refresh();

        if ($targetChange) {
            TrackerUpdated::dispatch($tracker);
        }

        if (($targetChange || $categoryChange) && $tracker->category) {
            CategoryUpdated::dispatch($tracker->category);
        }
    }

    public function updateAverage(Tracker $tracker): void
    {
        /** @var DataPoint $average */
        $average = $tracker->getAverageDataPoint();

        $average->value = $tracker
            ->dataPoints()
            ->where('date', '!=', $average->date)
            ->pluck('value')
            ->average() ?? 0;

        $average->save();
    }
}
