<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tracker;
use App\Models\User;
use App\Services\Mosaic\CategoryMosaicService;
use App\Services\Mosaic\DayMosaicService;
use App\Services\Mosaic\TrackerMosaicService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GraphDataController extends Controller
{
    public function __construct(
        protected TrackerMosaicService $trackerMosaicService,
        protected CategoryMosaicService $catMosaicService,
        protected DayMosaicService $dayMosaicService,
    ) {}

    public function day(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $days = $this->getDays($request);

        return response()->json([
            'data' => $this->dayMosaicService->getMosaicData($user, $days),
            'months' => $this->getMonthFragments($days),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function category(Category $category, Request $request): JsonResponse
    {
        $this->authorize('view', $category);

        $days = $this->getDays($request);

        return response()->json([
            'data' => $this->catMosaicService->getMosaicData($category, $days),
            'months' => $this->getMonthFragments($days),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function tracker(Tracker $tracker, Request $request): JsonResponse
    {
        $this->authorize('view', $tracker);

        $days = $this->getDays($request);

        return response()->json([
            'data' => $this->trackerMosaicService->getMosaicData($tracker, $days),
            'months' => $this->getMonthFragments($days),
        ]);
    }

    protected function getDays(Request $request): int
    {
        if ($request->has('days')) {
            return $request->integer('days');
        }

        $months = $request->integer('months', 1);

        $endDate = Carbon::today()->endOfWeek()->startOfDay();
        $startDate = $endDate->clone();

        while ($months--) {
            $startDate->subDay()->startOfMonth();
        }

        return intval(round($endDate->diffInDays($startDate, absolute: true)));
    }

    protected function getMonthFragments(int $days): array
    {
        $fragments = collect();

        $date = Carbon::today()->endOfWeek()->startOfDay();

        while ($days > 0) {
            $newDate = $date->clone()->startOfMonth();
            $count = min($days, $date->diffInDays($newDate, absolute: true)) + 1;
            $fragments->prepend($count);
            $date = $newDate->subDay();
            $days -= $count;
        }

        return $fragments->toArray();
    }
}
