<?php

namespace App\Http\Controllers\Api;

use App\Models\DataPoint;
use App\Services\DataPointService;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataPointResource;
use App\Http\Requests\UpdateDataPointRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Auth\Access\AuthorizationException;

class DataPointController extends Controller
{
    public function __construct(protected DataPointService $dataPointService)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDataPointRequest $request, DataPoint $dataPoint): JsonResource
    {
        $this->dataPointService->updateValue($dataPoint, $request->float('value'));

        return DataPointResource::make($dataPoint->refresh());
    }
}
