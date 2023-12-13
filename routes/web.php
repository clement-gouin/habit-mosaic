<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TrackerController;
use App\Http\Controllers\Web\DayViewController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DataPointController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ConfigurationController;
use App\Http\Controllers\Api\DayDataController;
use App\Http\Controllers\Web\AuthenticatedSessionController;

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
    Route::get('configuration', ConfigurationController::class)->name('configuration');

    Route::prefix('api')->group(function () {
        Route::prefix('day')->group(function () {
            Route::get('/', [DayDataController::class, 'data'])->name('day.data');
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
