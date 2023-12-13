<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\DayService;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrackerResource;
use App\Http\Resources\CategoryResource;
use Carbon\Exceptions\InvalidFormatException;

class DayViewController extends Controller
{
    public function __construct(protected DayService $dayService)
    {
    }

    public function __invoke(Request $request): View
    {
        return view('day_view', $this->dayService->getDayData($request->user(), $request->string('date', 'today')));
    }
}
