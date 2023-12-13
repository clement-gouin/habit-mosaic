<?php

namespace App\Http\Resources;

use App\Models\Tracker;
use App\Models\DataPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Resources\Json\JsonResource;

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
        try {
            $date = Carbon::parse($request->string('date', 'today'));
        } catch (InvalidFormatException) {
            $date = Carbon::today();
        }

        /** @var DataPoint $average */
        $average = $this->resource->getAverageDataPoint();

        return array_merge(parent::toArray($request), [
            'data_point' => DataPointResource::make($this->resource->getDataPointAt($date)),
            'average' => $average->value,
        ]);
    }
}
