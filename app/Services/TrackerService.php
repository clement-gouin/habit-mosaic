<?php

namespace App\Services;

use App\Models\Tracker;
use App\Models\DataPoint;
use App\Services\Mosaic\TrackerMosaicService;
use App\Services\Mosaic\CategoryMosaicService;

class TrackerService
{
    public function __construct(
        protected TrackerMosaicService $trackerMosaicService,
        protected CategoryMosaicService $catMosaicService,
    ) {
    }

    public function update(Tracker $tracker, array $attributes): void
    {
        $categoryChange = ($attributes['category_id'] ?? null) !== $tracker->category_id;
        $targetChange = $attributes['target_value'] !== $tracker->target_value ||
            $attributes['target_score'] !== $tracker->target_score;

        if ($categoryChange && $tracker->category) {
            $this->catMosaicService->wipeData($tracker->category);
        }

        $tracker->update($attributes);

        $tracker = $tracker->refresh();

        if ($targetChange || $categoryChange) {
            $this->updateAverage($tracker);
            $this->trackerMosaicService->wipeData($tracker);
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
