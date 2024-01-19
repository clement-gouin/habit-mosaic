<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TrackerResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('configuration', [
            'categories' => CategoryResource::collection($user->categories),
            'trackers' => TrackerResource::collection($user->trackers),
        ]);
    }
}
