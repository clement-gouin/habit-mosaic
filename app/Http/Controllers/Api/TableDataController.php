<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\DayService;
use Illuminate\Support\Carbon;
use App\Services\TableService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;
use Carbon\Exceptions\InvalidFormatException;

class TableDataController extends Controller
{
    public function __construct(protected TableService $tableService)
    {
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json($this->tableService->getTableData(
            $request->user(),
            $request->string('date', 'today'),
            $request->integer('days', 31))
        );
    }
}
