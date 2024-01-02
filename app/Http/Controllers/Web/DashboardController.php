<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\MosaicService;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('dashboard', [
            'categories' => CategoryResource::collection($user->categories),
            'trackers' => TrackerResource::collection($user->trackers),
        ]);
    }
}
