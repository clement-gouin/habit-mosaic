<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\StatisticsResource;
use App\Models\User;
use App\Services\Mosaic\DayMosaicService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GraphViewController extends Controller
{
    public function __construct(protected DayMosaicService $mosaicService) {}

    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $start = Carbon::today()->startOfWeek();
        $max = $this->mosaicService->getMaxDate($user);

        return view('graph_view', [
            'date' => $start->format('Y-m-d'),
            'max_days' => $start->diffInDays($max, absolute: true),
            'statistics' => StatisticsResource::make($this->mosaicService->getStatistics($user)),
        ]);
    }
}
