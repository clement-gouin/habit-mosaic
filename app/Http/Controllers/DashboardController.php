<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tracker;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;
use Carbon\Exceptions\InvalidFormatException;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('dashboard', $this->getData($request));
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json($this->getData($request));
    }

    protected function getData(Request $request): array
    {
        try {
            $date = Carbon::parse($request->string('date', 'today'));
        } catch (InvalidFormatException) {
            $date = Carbon::today();
        }

        /** @var User $user */
        $user = $request->user();

        return [
            'date' => $date->timestamp,
            'categories' => CategoryResource::collection($user->categories),
            'trackers' => TrackerResource::collection($user->trackers),
        ];
    }
}
