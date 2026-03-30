<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;

Route::controller(AdminController::class)->middleware(['auth:api', 'IsAdmin'])->group(function () {
    Route::get('/organizer-request-list', 'requestList');
    Route::patch('/approve-request/{user}', 'approveRequest');
    Route::post('/set-event-category', 'setEventCategories');
    Route::get('/get-event-category-list', 'eventCategoryIndex');
});

Route::controller(UserController::class)->middleware(['auth:api', 'IsAdmin'])->group( function () {
    Route::get('/get-all-users', 'index');
    Route::patch('/edit-user-data/{user}', 'update');
    Route::patch('/ban-user/{user}', 'banUser');
});