<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\TableService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

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
            $request->integer('days', 31)
        ));
    }
}
