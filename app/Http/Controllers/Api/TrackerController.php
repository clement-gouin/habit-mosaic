<?php

namespace App\Http\Controllers\Api;

use App\Events\TrackerDeleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrackerRequest;
use App\Http\Requests\UpdateTrackerRequest;
use App\Http\Resources\TrackerFullResource;
use App\Models\Tracker;
use App\Models\User;
use App\Services\TrackerService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class TrackerController extends Controller
{
    public function __construct(
        protected TrackerService $trackerService,
    ) {}

    public function list(Request $request): ResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return TrackerFullResource::collection($user->trackers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws AuthorizationException
     */
    public function store(StoreTrackerRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $tracker = new Tracker([
            'order' => $user->trackers->pluck('order')->max() + 1,
            ...$request->validated(),
        ]);

        $tracker->save();

        return TrackerFullResource::make($tracker->refresh())
            ->toResponse($request)
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrackerRequest $request, Tracker $tracker): JsonResource
    {
        $this->trackerService->update($tracker, $request->validated());

        return TrackerFullResource::make($tracker->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws AuthorizationException
     */
    public function destroy(Tracker $tracker): Response
    {
        $this->authorize('delete', $tracker);

        TrackerDeleted::dispatch($tracker);

        $tracker->delete();

        return response()->noContent();
    }
}
