<?php

namespace App\Http\Controllers\Api;

use App\Models\DataPoint;
use App\Http\Controllers\Controller;
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
        $dataPoint->updateValue($request->float('value'));

        return DataPointResource::make($dataPoint->refresh());
    }
}
