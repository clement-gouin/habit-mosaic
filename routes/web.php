<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DataPointController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\AuthenticatedSessionController;

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

    Route::prefix('dashboard')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('data', [DashboardController::class, 'data'])->name('dashboard.data');
    });

    Route::get('configuration', ConfigurationController::class)->name('configuration');

    Route::resource('trackers', TrackerController::class)->only([
        'store', 'update', 'destroy',
    ]);
    Route::get('trackers/list', [TrackerController::class, 'list'])->name('trackers.list');

    Route::resource('categories', CategoryController::class)->only([
        'store', 'update', 'destroy',
    ]);
    Route::get('categories/list', [CategoryController::class, 'list'])->name('categories.list');

    Route::resource('data_points', DataPointController::class)->only([
        'update',
    ]);
});
