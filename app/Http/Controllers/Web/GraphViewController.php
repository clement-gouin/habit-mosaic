<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GraphViewController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('graph_view', []);
    }
}
