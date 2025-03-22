<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SeatController;
use App\Http\Middleware\AdminCoMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('/buses', BusController::class)->middleware('co-co_leader');
        Route::apiResource('/schedules', ScheduleController::class);
        Route::apiResource('/seats', SeatController::class);
        
        Route::post('/register', [AuthController::class, 'register'])->withoutMiddleware('auth:sanctum');
        Route::post('/login', [AuthController::class, 'login'])->name('login')->withoutMiddleware('auth:sanctum');
        Route::get('/logout', [AuthController::class, 'logout']);

});