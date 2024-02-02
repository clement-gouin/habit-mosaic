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
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'category' => CategoryResource::make($this->resource->category),
            'name' => $this->resource->name,
            'icon' => $this->resource->icon,
            'order' => $this->resource->order,
            'unit' => $this->resource->unit,
            'value_step' => $this->resource->value_step,
            'target_value' => $this->resource->target_value,
            'target_score' => $this->resource->target_score,
            'single' => $this->resource->single,
            'overflow' => $this->resource->overflow,
        ];
    }
}
