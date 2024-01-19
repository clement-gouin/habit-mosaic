<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TrackerResource;
use App\Models\User;
use App\Services\DayService;
use App\Services\TableService;
use App\Utils\Date;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableViewController extends Controller
{
    public function __construct(protected TableService $tableService, protected DayService $dayService)
    {
    }

    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $endDate = Date::parse($request->string('date', 'today'));
        $days = $request->integer('days', 31);

        return view('table_view', [
            'date' => $endDate->format('Y-m-d'),
            'days' => $days,
            'average' => $this->dayService->getAverage($user),
            'categories' => CategoryResource::collection($user->categories),
            'trackers' => TrackerResource::collection($user->trackers),
            'data' => $this->tableService->getTableData($user, $endDate, $days),
        ]);
    }
}
