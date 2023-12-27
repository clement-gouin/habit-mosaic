<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\TableService;
use App\Http\Controllers\Controller;

class TableViewController extends Controller
{
    public function __construct(protected TableService $tableService)
    {
    }

    public function __invoke(Request $request): View
    {
        return view('table_view', $this->tableService->getTableData(
            $request->user(),
            $request->string('date', 'today'),
            $request->integer('days', 31))
        );
    }
}
