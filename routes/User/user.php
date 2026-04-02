<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\OrganizerRequestController;
use App\Http\Controllers\User\EventController;


Route::controller(OrganizerRequestController::class)->group(function () {
    Route::post('/organizer-request', 'organizerRequest')->middleware('auth:api');
});

Route::middleware('auth:api')->controller(UserController::class)->group(function () {
	Route::get('/show-user', 'show');
	Route::patch('/update-record', 'update');
	Route::post('/change-password', 'changePassword');
	Route::delete('/delete-user', 'destroy');
});

Route::controller(EventController::class)->middleware('auth:api')->group(function () {
	Route::get('/check-events', 'index');
});