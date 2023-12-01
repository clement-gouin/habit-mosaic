<?php

namespace App\Http\Controllers;

use App\Models\DataPoint;
use App\Http\Resources\DataPointResource;
use App\Http\Requests\UpdateDataPointRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Auth\Access\AuthorizationException;

class DataPointController extends Controller
{
    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateDataPointRequest $request, DataPoint $dataPoint): JsonResource
    {
        $dataPoint->update($request->validated());

        return DataPointResource::make($dataPoint->refresh());
    }
}
