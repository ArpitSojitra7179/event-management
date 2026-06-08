<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController;

Route::controller(AdminController::class)->middleware(['auth:api', 'IsAdmin'])->group(function () {
    Route::get('/organizers/request-list', 'organizerRequests');
    Route::get('/events/request-list', 'eventRequests');
    Route::patch('/user-requests/{user}/{status}', 'toggle');
});

Route::controller(UserController::class)->middleware(['auth:api', 'IsAdmin'])->group( function () {
    Route::get('/users', 'index');
    Route::patch('/users/{user}', 'update');
    Route::patch('/users/{user}/{status}', 'toggle');
    Route::get('/users/{user}', 'show');
    Route::delete('/users/{user}', 'destroy');
});

Route::controller(EventController::class)->middleware(['auth:api', 'IsAdmin'])->group(function () {
    Route::post('/categories', 'storeCategories');
    Route::get('/categories', 'categoryIndex');
    Route::get('/events', 'index');
    Route::patch('/event-requests/{event}/{status}', 'toggle');
    Route::get('/events/{event}', 'show');
    Route::patch('/events/{event}', 'update');
    Route::delete('/events/{event}', 'destroy');
});