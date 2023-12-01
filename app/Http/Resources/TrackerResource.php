<?php

namespace App\Http\Resources;

use App\Models\Tracker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Tracker $resource */
class TrackerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'icon' => $this->resource->icon,
            'unit' => $this->resource->unit,
            'value_step' => $this->resource->value_step,
            'default_value' => $this->resource->default_value,
            'target_value' => $this->resource->target_value,
            'target_score' => $this->resource->target_score,
            'data_points' => $this->when($request->boolean('with_data'), fn () => DataPointResource::collection($this->resource->dataPoints)),
            'last_updated' => $this->resource->dataPoints()->first()?->value('date'),
        ];
    }
}
