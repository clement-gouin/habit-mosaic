<?php

namespace App\Http\Resources;

use App\Models\DataPoint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property DataPoint $resource */
class DataPointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'tracker_id' => $this->resource->tracker_id,
            'date' => $this->resource->date,
            'value' => $this->resource->value,
            'score' => $this->resource->score(),
            'updated_at' => $this->resource->updated_at->toISOString(),
        ];
    }

    /**
     * Top Level Meta Data
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'tracker' => TrackerResource::make($this->resource->tracker),
        ];
    }
}
