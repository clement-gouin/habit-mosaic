<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DataPointController;
use App\Http\Controllers\Api\DayDataController;
use App\Http\Controllers\Api\GraphDataController;
use App\Http\Controllers\Api\MosaicDataController;
use App\Http\Controllers\Api\TableDataController;
use App\Http\Controllers\Api\TrackerController;
use App\Http\Controllers\Web\AuthenticatedSessionController;
use App\Http\Controllers\Web\ConfigurationController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DayViewController;
use App\Http\Controllers\Web\GraphViewController;
use App\Http\Controllers\Web\TableViewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['guest', 'throttle'])->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/login/{token}', [AuthenticatedSessionController::class, 'login'])->name('login.token');
});

Route::middleware('auth')->group(function () {
    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('day', DayViewController::class)->name('day');
    Route::get('graph', GraphViewController::class)->name('graph');
    Route::get('table', TableViewController::class)->name('table');
    Route::get('configuration', ConfigurationController::class)->name('configuration');

    Route::prefix('api')->group(function () {
        Route::prefix('day')->group(function () {
            Route::get('/', [DayDataController::class, 'data'])->name('day.data');
        });

        Route::prefix('mosaic')->group(function () {
            Route::get('day', [MosaicDataController::class, 'day'])->name('mosaic.day');
            Route::get('categories/{category}', [MosaicDataController::class, 'category'])->name('mosaic.category');
            Route::get('trackers/{tracker}', [MosaicDataController::class, 'tracker'])->name('mosaic.tracker');
        });

        Route::prefix('graph')->group(function () {
            Route::get('day', [GraphDataController::class, 'day'])->name('graph.day');
            Route::get('categories/{category}', [GraphDataController::class, 'category'])->name('graph.category');
            Route::get('trackers/{tracker}', [GraphDataController::class, 'tracker'])->name('graph.tracker');
        });

        Route::prefix('table')->group(function () {
            Route::get('/', [TableDataController::class, 'data'])->name('table.data');
        });

        Route::prefix('categories')->group(function () {
            Route::get('list', [CategoryController::class, 'list'])->name('categories.list');
            Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
            Route::put('{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        });

        Route::prefix('trackers')->group(function () {
            Route::get('list', [TrackerController::class, 'list'])->name('trackers.list');
            Route::post('/', [TrackerController::class, 'store'])->name('trackers.store');
            Route::put('{tracker}', [TrackerController::class, 'update'])->name('trackers.update');
            Route::delete('{tracker}', [TrackerController::class, 'destroy'])->name('trackers.destroy');
        });

        Route::prefix('data_points')->group(function () {
            Route::put('{data_point}', [DataPointController::class, 'update'])->name('data_points.update');
        });
    });
});

Route::fallback(function (Request $request) {
    if (! str_starts_with($request->path(), 'static/')) {
        return redirect('/static/'.$request->path());
    }
    abort(404);
});
