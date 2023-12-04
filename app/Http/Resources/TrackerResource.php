<?php

namespace App\Http\Resources;

use App\Models\Tracker;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Carbon\Exceptions\InvalidFormatException;
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
        try {
            $date = Carbon::parse($request->string('date', 'now'));
        } catch (InvalidFormatException) {
            $date = Carbon::today();
        }

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'icon' => $this->resource->icon,
            'order' => $this->resource->order,
            'unit' => $this->resource->unit,
            'value_step' => $this->resource->value_step,
            'default_value' => $this->resource->default_value,
            'target_value' => $this->resource->target_value,
            'target_score' => $this->resource->target_score,
            'target_max' => $this->resource->target_max,
            'data_point' => DataPointResource::make($this->resource->getDataPointAt($date)),
        ];
    }
}
