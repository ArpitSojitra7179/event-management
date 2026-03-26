<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organizer\OrganizerController;

Route::controller(OrganizerController::class)->middleware(['auth:api', 'IsOrganizer'])->group(function () {
	Route::get('/all-categories', 'index');
	Route::post('/create-event', 'createEvent');
});