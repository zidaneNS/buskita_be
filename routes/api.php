<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\UserController;
use App\Models\Route as ModelsRoute;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('/buses', BusController::class)->middleware('co-co_leader');

        Route::apiResource('/schedules', ScheduleController::class);
        Route::get('schedules/route/{route}', [ScheduleController::class, 'byRoute']);

        Route::apiResource('/seats', SeatController::class)->except(['index', 'show']);
        Route::get('/seats/schedule/{schedule}', [SeatController::class, 'index']);
        Route::get('/seats/{seat}/verify', [SeatController::class, 'verify']);

        Route::apiResource('/users', UserController::class);
        Route::get('/user', [UserController::class, 'getData']);
        Route::get('/co', [UserController::class, 'co']);
        Route::get('/passengers', [UserController::class, 'passenger']);

        Route::post('/register', [AuthController::class, 'register'])->withoutMiddleware('auth:sanctum');
        Route::post('/login', [AuthController::class, 'login'])->name('login')->withoutMiddleware('auth:sanctum');
        Route::get('/logout', [AuthController::class, 'logout']);

        Route::get('routes', function () {
                $routes = ModelsRoute::all();

                return response($routes);
        });
});