<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TrackerResource;
use App\Models\User;
use App\Services\DayService;
use App\Services\TableService;
use App\Utils\Date;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableDataController extends Controller
{
    public function __construct(protected TableService $tableService, protected DayService $dayService)
    {
    }

    public function data(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $endDate = Date::parse($request->string('date', 'today'));
        $days = $request->integer('days', 31);

        return response()->json([
            'average' => $this->dayService->getAverage($user),
            'categories' => CategoryResource::collection($user->categories),
            'trackers' => TrackerResource::collection($user->trackers),
            'data' => $this->tableService->getTableData($user, $endDate, $days),
        ]);
    }
}
