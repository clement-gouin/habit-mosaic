<?php

namespace App\Http\Controllers\Api;

use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\MosaicService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;

class MosaicDataController extends Controller
{
    public function __construct(protected MosaicService $mosaicService)
    {
    }

    public function day(Request $request): JsonResponse
    {
        return response()->json($this->mosaicService->getDayMosaicData($request->user(), $request->integer('days', 70)));
    }

    /**
     * @throws AuthorizationException
     */
    public function category(Category $category, Request $request): JsonResponse
    {
        $this->authorize('view', $category);

        return response()->json($this->mosaicService->getCategoryMosaicData($category, $request->integer('days', 70)));
    }

    /**
     * @throws AuthorizationException
     */
    public function tracker(Tracker $tracker, Request $request): JsonResponse
    {
        $this->authorize('view', $tracker);

        return response()->json($this->mosaicService->getTrackerMosaicData($tracker, $request->integer('days', 70)));
    }
}
