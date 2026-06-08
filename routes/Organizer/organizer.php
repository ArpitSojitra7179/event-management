<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\Organizer\EventApproveController;

Route::controller(EventController::class)->middleware(['auth:api', 'IsOrganizer'])->group(function () {
	Route::get('/categories', 'categoryIndex');
	Route::post('/events', 'store');
	Route::post('/events/{event}/request', 'eventRequest');
	Route::get('/events', 'index');
	Route::get('/events/{event}', 'show');
	Route::patch('/events/{event}', 'update');
	Route::delete('/events/{event}', 'destroy');
});
