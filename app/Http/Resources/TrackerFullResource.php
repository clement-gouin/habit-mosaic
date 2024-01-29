<?php

namespace App\Http\Resources;

use App\Models\Tracker;
use App\Services\Mosaic\TrackerMosaicService;
use App\Utils\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

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

        /** @var TrackerMosaicService $mosaicService */
        $mosaicService = App::make(TrackerMosaicService::class);

        return array_merge(parent::toArray($request), [
            'data_point' => DataPointResource::make($this->resource->getDataPointAt($date)),
            'statistics' => StatisticsResource::make($mosaicService->getStatistics($this->resource)),
        ]);
    }
}
