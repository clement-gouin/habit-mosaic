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
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'date' => $this->resource->date,
            'value' => $this->resource->value,
            'tracker' => $this->when($request->boolean('with_tracker'), fn () => TrackerResource::make($this->resource->tracker)),
        ];
    }
}
