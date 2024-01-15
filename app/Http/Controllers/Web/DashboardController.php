<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Tracker;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\DayService;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrackerFullResource;
use App\Http\Resources\CategoryFullResource;

class DashboardController extends Controller
{
    public function __construct(protected DayService $dayService)
    {
    }

    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('dashboard', [
            'average' => $this->dayService->getAverage($user),
            'categories' => CategoryFullResource::collection($user->categories),
            'trackers' => TrackerFullResource::collection($user->trackers),
        ]);
    }
}
