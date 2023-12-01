<?php

namespace App\Http\Controllers;

use App\Models\DataPoint;
use Illuminate\Http\Response;
use App\Http\Requests\UpdateDataPointRequest;
use Illuminate\Auth\Access\AuthorizationException;

class DataPointController extends Controller
{
    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateDataPointRequest $request, DataPoint $dataPoint): Response
    {
        $dataPoint->update($request->validated());

        return response()->noContent(501); // TODO
    }
}
