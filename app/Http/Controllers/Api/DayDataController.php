<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\DayService;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;
use Carbon\Exceptions\InvalidFormatException;

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
