<?php

namespace App\Http\Controllers\Api;

use App\Models\Tracker;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\MosaicService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use App\Services\Mosaic\CategoryMosaicService;
use Illuminate\Auth\Access\AuthorizationException;

class MosaicDataController extends Controller
{
    public function __construct(
        protected TrackerMosaicService $trackerMosaicService,
        protected CategoryMosaicService $catMosaicService,
        protected DayMosaicService $dayMosaicService,
    ) {
    }

    public function day(Request $request): JsonResponse
    {
        return response()->json($this->dayMosaicService->getMosaicData($request->user(), $request->integer('days', 70)));
    }

    /**
     * @throws AuthorizationException
     */
    public function category(Category $category, Request $request): JsonResponse
    {
        $this->authorize('view', $category);

        return response()->json($this->catMosaicService->getMosaicData($category, $request->integer('days', 70)));
    }

    /**
     * @throws AuthorizationException
     */
    public function tracker(Tracker $tracker, Request $request): JsonResponse
    {
        $this->authorize('view', $tracker);

        return response()->json($this->trackerMosaicService->getMosaicData($tracker, $request->integer('days', 70)));
    }
}
