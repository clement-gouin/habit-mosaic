<?php

namespace App\Services;

use App\Models\DataPoint;
use App\Services\Mosaic\TrackerMosaicService;

class DataPointService
{
    public function __construct(protected TrackerMosaicService $trackerMosaicService)
    {
    }

    public function updateValue(DataPoint $dataPoint, float $value): void
    {
        $value = max($value, 0);

        if (! $dataPoint->tracker->overflow) {
            $value = min($value, $dataPoint->tracker->target_value);
        }

        if ($dataPoint->value !== $value) {
            $dataPoint->update(['value' => $value]);

            $this->trackerMosaicService->clearData($dataPoint->tracker, $dataPoint->date);
        }
    }
}
