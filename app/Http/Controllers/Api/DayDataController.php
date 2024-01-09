<?php

namespace App\Http\Controllers\Api;

use App\Utils\Date;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\DayService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TrackerFullResource;

class DayDataController extends Controller
{
    public function __construct(protected DayService $dayService)
    {
    }

    public function data(Request $request): JsonResponse
    {
        $date = Date::parse($request->string('date', 'today'));

        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'average' => $this->dayService->getAverage($user),
            'categories' => CategoryResource::collection($user->categories),
            'trackers' => TrackerFullResource::collection($user->trackers),
        ]);
    }
}
