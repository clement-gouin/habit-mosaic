<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Mosaic\DayMosaicService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GraphViewController extends Controller
{
    public function __construct(
        protected DayMosaicService $dayMosaicService,
    ) {}

    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('graph_view', [
            'date' => Carbon::today()->startOfWeek()->format('Y-m-d'),
            'data' => $this->dayMosaicService->getMosaicData($user, $request->integer('days', 70)),
            'average' => $this->dayMosaicService->getAverageData($user, $request->integer('days', 70)),
        ]);
    }
}
