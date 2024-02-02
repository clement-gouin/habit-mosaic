<?php

namespace App\Services;

use App\Events\CategoryUpdated;
use App\Events\TrackerScoreUpdated;
use App\Models\Tracker;

class TrackerService extends Service
{
    public function update(Tracker $tracker, array $attributes): void
    {
        $categoryChange = $attributes['category_id'] !== $tracker->category_id;
        $targetChange = $attributes['target_value'] !== $tracker->target_value ||
            $attributes['target_score'] !== $tracker->target_score;

        if ($categoryChange) {
            CategoryUpdated::dispatch($tracker->category);
        }

        $tracker->fill($attributes)->save();

        $tracker = $tracker->refresh();

        if ($targetChange) {
            TrackerScoreUpdated::dispatch($tracker);
        }

        if ($targetChange || $categoryChange) {
            CategoryUpdated::dispatch($tracker->category);
        }
    }
}
