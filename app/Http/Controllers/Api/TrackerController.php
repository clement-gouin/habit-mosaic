<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Tracker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\TrackerService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrackerResource;
use App\Http\Requests\StoreTrackerRequest;
use App\Http\Requests\UpdateTrackerRequest;
use App\Services\Mosaic\TrackerMosaicService;
use App\Services\Mosaic\CategoryMosaicService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TrackerController extends Controller
{
    public function __construct(
        protected TrackerService $trackerService,
        protected TrackerMosaicService $trackerMosaicService,
    ) {
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
        /** @var User $user */
        $user = $request->user();

        $tracker = new Tracker([
            'order' => $user->trackers->pluck('order')->max() + 1,
            ...$request->validated()
        ]);

        $user->trackers()->save($tracker);

        return TrackerResource::make($tracker->refresh())
            ->toResponse($request)
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrackerRequest $request, Tracker $tracker): JsonResource
    {
        $this->trackerService->update($tracker, $request->validated());

        return TrackerResource::make($tracker->refresh());
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Tracker $tracker): Response
    {
        $this->authorize('delete', $tracker);

        $this->trackerMosaicService->wipeData($tracker);

        $tracker->delete();

        return response()->noContent();
    }
}
