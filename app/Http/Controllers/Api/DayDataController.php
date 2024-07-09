<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryFullResource;
use App\Http\Resources\StatisticsResource;
use App\Http\Resources\TrackerFullResource;
use App\Models\User;
use App\Services\Mosaic\DayMosaicService;
use App\Utils\Date;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DayDataController extends Controller
{
    public function __construct(protected DayMosaicService $mosaicService) {}

    public function data(Request $request): JsonResponse
    {
        $date = Date::parse($request->string('date', 'today'));

        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'statistics' => StatisticsResource::make($this->mosaicService->getStatistics($user)),
            'categories' => CategoryFullResource::collection($user->categories),
            'trackers' => TrackerFullResource::collection($user->trackers),
        ]);
    }
}
