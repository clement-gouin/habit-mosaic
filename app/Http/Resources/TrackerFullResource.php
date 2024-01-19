<?php

namespace App\Http\Resources;

use App\Models\DataPoint;
use App\Models\Tracker;
use App\Utils\Date;
use Illuminate\Http\Request;

/** @property Tracker $resource */
class TrackerFullResource extends TrackerResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $date = Date::parse($request->string('date', 'today'));

        /** @var DataPoint $average */
        $average = $this->resource->getAverageDataPoint();

        return array_merge(parent::toArray($request), [
            'data_point' => DataPointResource::make($this->resource->getDataPointAt($date)),
            'average' => $average->value,
        ]);
    }
}
