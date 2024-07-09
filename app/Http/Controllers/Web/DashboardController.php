<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryFullResource;
use App\Http\Resources\StatisticsResource;
use App\Http\Resources\TrackerFullResource;
use App\Models\User;
use App\Services\Mosaic\DayMosaicService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected DayMosaicService $mosaicService) {}

    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('dashboard', [
            'statistics' => StatisticsResource::make($this->mosaicService->getStatistics($user)),
            'categories' => CategoryFullResource::collection($user->categories),
            'trackers' => TrackerFullResource::collection($user->trackers),
        ]);
    }
}
