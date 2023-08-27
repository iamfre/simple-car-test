<?php

use App\Http\Controllers\Api\CarController;
use Illuminate\Support\Facades\Route;

Route::prefix('cars')->group(function () {
    Route::group([
        'middleware' => ['auth_api'],
        'roles' => [],
    ], function () {
        Route::post('/', [CarController::class, 'update']);
    });

    Route::get('/', [CarController::class, 'getList']);
    Route::get('/{id}', [CarController::class, 'show']);
});