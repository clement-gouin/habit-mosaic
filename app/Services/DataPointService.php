<?php

namespace App\Services;

use App\Models\DataPoint;

class DataPointService
{


    public function updateValue(DataPoint $dataPoint, float $value): void
    {
        $value = max($value, 0);

        if (! $dataPoint->tracker->overflow) {
            $value = min($value, $dataPoint->tracker->target_value);
        }

        $dataPoint->update(['value' => $value]);
    }
}
