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
        $lastCategory = $tracker->category;

        $tracker->update($attributes);

        $tracker = $tracker->refresh();

        $this->updateAverage($tracker);

        $this->trackerMosaicService->wipeData($tracker);

        if ($lastCategory && $tracker->category && $tracker->category->id !== $lastCategory->id) {
            $this->catMosaicService->wipeData($lastCategory);
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
