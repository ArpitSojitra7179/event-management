<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organizer\OrganizerController;

Route::controller(OrganizerController::class)->group(function () {
	Route::get('/all-categories', 'index');
});