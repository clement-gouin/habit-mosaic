<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDataPointRequest;
use App\Http\Resources\DataPointResource;
use App\Models\DataPoint;
use App\Services\DataPointService;
use Illuminate\Http\Resources\Json\JsonResource;

class DataPointController extends Controller
{
    public function __construct(protected DataPointService $dataPointService) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDataPointRequest $request, DataPoint $dataPoint): JsonResource
    {
        $this->dataPointService->updateValue($dataPoint, $request->float('value'));

        return DataPointResource::make($dataPoint->refresh());
    }
}
