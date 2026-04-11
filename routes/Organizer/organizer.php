<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organizer\OrganizerController;
use App\Http\Controllers\Organizer\EventApproveController;

Route::controller(OrganizerController::class)->middleware(['auth:api', 'IsOrganizer'])->group(function () {
	Route::get('/all-categories', 'index');
	Route::post('/create-event', 'createEvent');
});

Route::controller(EventApproveController::class)->middleware(['auth:api', 'IsOrganizer'])->group(function () {
	Route::post('event-publish-request', 'eventPublishRequest');
});