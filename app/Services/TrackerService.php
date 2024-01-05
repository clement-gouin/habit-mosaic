<?php

namespace App\Services;

use App\Models\Tracker;
use App\Models\DataPoint;
use App\Events\TrackerUpdated;
use App\Events\CategoryUpdated;

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

        $tracker->update($attributes);

        $tracker = $tracker->refresh();

        if ($targetChange || $categoryChange) {
            TrackerUpdated::dispatch($tracker);

            $this->updateAverage($tracker);
        }
    }

    public function updateAverage(Tracker $tracker): void
    {
        /** @var DataPoint $average */
        $average = $tracker->getAverageDataPoint();

        $average->update([
            'value' => $tracker->dataPoints()->where('date', '!=', $average->date)->pluck('value')->average() ?? 0,
        ]);
    }
}
