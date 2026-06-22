<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\OrganizerRequestController;
use App\Http\Controllers\User\EventController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\TicketController;


Route::controller(OrganizerRequestController::class)->group(function () {
    Route::post('/organizer-request', 'organizerRequest')->middleware('auth:api');
});

Route::middleware('auth:api')->controller(UserController::class)->group(function () {
	Route::get('/user', 'show');
	Route::patch('/update', 'update');
	Route::post('/change-password', 'changePassword');
	Route::delete('/delete', 'destroy');
});

Route::controller(EventController::class)->middleware('auth:api')->group(function () {
	Route::get('/events', 'index');
	Route::get('/event/{event}', 'show');
});

Route::controller(PaymentController::class)->middleware('auth:api')->group(function () {
	Route::post('/execute/{key}', 'execute');
	Route::post('/verify/{key}', 'verify');
});

Route::controller(PaymentController::class)->group(function () {
	Route::get('/payment/success', 'success')->name('payment.success');
	Route::get('/payment/cancel', 'cancel')->name('payment.cancel');
});

Route::controller(TicketController::class)->middleware('auth:api')->group(function () {
	Route::post('/tickets/book/{event}', 'book');
});