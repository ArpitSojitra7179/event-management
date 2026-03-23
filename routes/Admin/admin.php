<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::controller(AdminController::class)->middleware(['auth:api', 'IsAdmin'])->group(function () {
    Route::get('/organizer-request-list', 'requestList');
});