<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CarController;
use App\Http\Controllers\API\RentalController;
use App\Http\Controllers\API\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('cars', CarController::class);
    Route::apiResource('rentals', RentalController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('rentals/{rental}/payments', [PaymentController::class, 'store']);
    Route::get('payments', [PaymentController::class, 'index']);
    Route::get('payments/{payment}', [PaymentController::class, 'show']);
    Route::post('/rentals/{rental}/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);

});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('cars', [CarController::class, 'index']);
Route::get('cars/{id}', [CarController::class, 'show']);

Route::get('rentals', [RentalController::class, 'index']);
Route::get('rentals/{id}', [RentalController::class, 'show']);