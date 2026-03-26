<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::controller(AdminController::class)->middleware(['auth:api', 'IsAdmin'])->group(function () {
    Route::get('/organizer-request-list', 'requestList');
    Route::patch('/approve-request/{user}', 'approveRequest');
    Route::post('/set-event-category', 'setEventCategories');
    Route::get('/get-all-users', 'allUsers');
    Route::get('/get-event-category-list', 'eventCategoryIndex');
});