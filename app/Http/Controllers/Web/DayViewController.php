<?php

namespace App\Http\Controllers\Web;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\DayService;
use App\Http\Controllers\Controller;

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
