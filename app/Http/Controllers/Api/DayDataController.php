<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\DayService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class DayDataController extends Controller
{
    public function __construct(protected DayService $dayService)
    {
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json($this->dayService->getDayData($request->user(), $request->string('date', 'today')));
    }
}
