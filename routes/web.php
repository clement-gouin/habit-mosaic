<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Authenticate;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\DataPointController;
use App\Http\Controllers\DashboardController;
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

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('trackers', TrackerController::class)->only([
        'index', 'store', 'update', 'destroy',
    ]);
    Route::get('trackers/list', [TrackerController::class, 'list'])->name('trackers.list');

    Route::resource('data_points', DataPointController::class)->only([
        'update',
    ]);
});
