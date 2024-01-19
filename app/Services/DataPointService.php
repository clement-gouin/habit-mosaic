<?php

namespace App\Services;

use App\Events\DataPointUpdated;
use App\Models\DataPoint;

class DataPointService
{
    public function updateValue(DataPoint $dataPoint, float $value): void
    {
        $value = max($value, 0);

        if (! $dataPoint->tracker->overflow) {
            $value = min($value, $dataPoint->tracker->target_value);
        }

        $value = round($value / $dataPoint->tracker->value_step) * $dataPoint->tracker->value_step;

        if ($dataPoint->value !== $value) {
            $dataPoint->update(['value' => $value]);

            DataPointUpdated::dispatch($dataPoint);
        }
    }
}
