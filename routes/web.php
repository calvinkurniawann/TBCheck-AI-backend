<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TbScreeningController;

Route::post('/screening/calculate', [TbScreeningController::class, 'calculateRisk']);
Route::get('/screening/history/{user_id}', [TbScreeningController::class, 'getHistory']);

require __DIR__.'/auth.php';
