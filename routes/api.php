<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TbScreeningController;

// ─── Public Auth Routes ───
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ─── Protected Routes (requires Sanctum token) ───
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Screening
    Route::post('/screening/calculate', [TbScreeningController::class, 'calculateRisk']);
    Route::get('/screening/history', [TbScreeningController::class, 'getHistory']);
});
