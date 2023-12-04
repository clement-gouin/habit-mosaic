<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tracker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TrackerResource;
use App\Http\Requests\StoreTrackerRequest;
use App\Http\Requests\UpdateTrackerRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return response()->noContent(501); // TODO
    }

    public function list(Request $request): ResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return TrackerResource::collection($user->trackers);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(StoreTrackerRequest $request): JsonResponse
    {
        $tracker = new Tracker($request->validated());

        /** @var User $user */
        $user = $request->user();

        $user->trackers()->save($tracker);

        return TrackerResource::make($tracker->refresh())
            ->toResponse($request)
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateTrackerRequest $request, Tracker $tracker): JsonResource
    {
        $tracker->update($request->validated());

        return TrackerResource::make($tracker->refresh());
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
