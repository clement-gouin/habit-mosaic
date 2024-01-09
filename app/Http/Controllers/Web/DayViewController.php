<?php

namespace App\Http\Controllers\Web;

use App\Utils\Date;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\DayService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TrackerFullResource;

class DayViewController extends Controller
{
    public function __construct(protected DayService $dayService)
    {
    }

    public function __invoke(Request $request): View
    {
        $date = Date::parse($request->string('date', 'today'));

        /** @var User $user */
        $user = $request->user();

        return view('day_view', [
            'date' => $date->format('Y-m-d'),
            'average' => $this->dayService->getAverage($user),
            'categories' => CategoryResource::collection($user->categories),
            'trackers' => TrackerFullResource::collection($user->trackers),
        ]);
    }
}
