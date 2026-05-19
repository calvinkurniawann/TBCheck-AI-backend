<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TbScreeningController;

Route::post('/screening/calculate', [TbScreeningController::class, 'calculateRisk']);

require __DIR__.'/auth.php';
