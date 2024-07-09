<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\StatisticsResource;
use App\Http\Resources\TrackerResource;
use App\Models\User;
use App\Services\Mosaic\DayMosaicService;
use App\Services\TableService;
use App\Utils\Date;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableViewController extends Controller
{
    public function __construct(protected TableService $tableService, protected DayMosaicService $mosaicService) {}

    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $endDate = Date::parse($request->string('date', 'today'));
        $days = $request->integer('days', 31);

        return view('table_view', [
            'date' => $endDate->format('Y-m-d'),
            'days' => $days,
            'statistics' => StatisticsResource::make($this->mosaicService->getStatistics($user)),
            'categories' => CategoryResource::collection($user->categories),
            'trackers' => TrackerResource::collection($user->trackers),
        ]);
    }
}
