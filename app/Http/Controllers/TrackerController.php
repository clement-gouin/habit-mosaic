<?php

namespace App\Http\Controllers;

use App\Models\Tracker;
use Illuminate\Http\Response;
use App\Http\Requests\StoreTrackerRequest;
use App\Http\Requests\UpdateTrackerRequest;
use Illuminate\Auth\Access\AuthorizationException;

class TrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return response()->noContent(501); // TODO
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(StoreTrackerRequest $request): Response
    {
        $tracker = Tracker::create($request->validated());

        return response()->noContent(501); // TODO
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateTrackerRequest $request, Tracker $tracker): Response
    {
        $tracker->update($request->validated());

        return response()->noContent(501); // TODO
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Tracker $tracker): Response
    {
        $this->authorize('delete', $tracker);

        $tracker->delete();

        return response()->noContent();
    }
}
