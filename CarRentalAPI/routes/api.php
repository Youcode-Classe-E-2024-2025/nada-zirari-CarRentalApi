<?php

use App\Http\Controllers\API\CarController;
use App\Http\Controllers\API\RentalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('cars', CarController::class);
Route::apiResource('rentals', RentalController::class);
