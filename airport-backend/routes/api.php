<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AirportController;
use App\Http\Controllers\Api\AirlineController;
use App\Http\Controllers\Api\AircraftController;
use App\Http\Controllers\Api\PassengerController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UserController;

// Public routes - NO middleware
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes - Only auth:sanctum middleware
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Admin only routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('airports', AirportController::class);
        Route::apiResource('airlines', AirlineController::class);
        Route::apiResource('aircraft', AircraftController::class);
        Route::apiResource('users', UserController::class);
    });

    // Staff routes (admin + staff)
    Route::middleware('role:admin,staff')->prefix('staff')->group(function () {
        Route::apiResource('flights', FlightController::class);
        Route::patch('flights/{flight}/status', [FlightController::class, 'updateStatus']);
        Route::apiResource('passengers', PassengerController::class);
        Route::get('bookings', [BookingController::class, 'index']);
        Route::get('bookings/{booking}', [BookingController::class, 'show']);
        Route::patch('bookings/{booking}', [BookingController::class, 'update']);
        Route::delete('bookings/{booking}', [BookingController::class, 'destroy']);
    });

    // Agent routes (admin + staff + agent)
    Route::middleware('role:admin,staff,agent')->prefix('agent')->group(function () {
        Route::get('flights/search', [FlightController::class, 'search']);
        Route::post('bookings', [BookingController::class, 'store']);
        Route::get('bookings', [BookingController::class, 'index']);
        Route::get('bookings/{booking}', [BookingController::class, 'show']);
        Route::apiResource('passengers', PassengerController::class);
        Route::apiResource('payments', PaymentController::class);
    });

    // Shared routes (all authenticated users)
    Route::get('flights', [FlightController::class, 'index']);
    Route::get('flights/{flight}', [FlightController::class, 'show']);
    Route::get('airports', [AirportController::class, 'index']);
    Route::get('airlines', [AirlineController::class, 'index']);
    Route::get('aircraft', [AircraftController::class, 'index']);
});