<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;

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
