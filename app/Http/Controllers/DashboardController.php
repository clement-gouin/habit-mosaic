<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tracker;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\TrackerResource;
use Carbon\Exceptions\InvalidFormatException;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        try {
            $date = Carbon::parse($request->string('date', 'now'));
        } catch (InvalidFormatException) {
            $date = Carbon::today();
        }

        /** @var User $user */
        $user = $request->user();

        return view('dashboard', [
            'date' => $date->timestamp,
            'trackers' => TrackerResource::collection($user->trackers),
        ]);
    }
}
