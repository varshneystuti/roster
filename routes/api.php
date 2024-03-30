<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RosterUploadController;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/upload-roster', [RosterUploadController::class, 'upload'])->name('upload.roster');
Route::get('/events', [EventController::class, 'getEventsBetweenDates'])->name('events.betweenDates');
Route::get('/flights/next-week', [EventController::class, 'getFlightsForNextWeek'])->name('flights.nextWeek');
Route::get('/flights/next-week-sby', [EventController::class, 'getFlightsForNextWeekStandBy'])->name('flights.nextWeekSby');
Route::get('/flights/from-location', [EventController::class, 'getFlightsFromLocation'])->name('flights.fromLocation');
