<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TbScreeningController;

Route::post('/screening/calculate', [TbScreeningController::class, 'calculateRisk']);
Route::get('/screening/history/{userId}', [TbScreeningController::class, 'getHistory']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
