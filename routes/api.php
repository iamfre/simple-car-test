<?php

use App\Http\Controllers\Api\CarController;

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/cars',
    'middleware' => [],
    'roles' => [],
], function () {
    Route::get('/', [CarController::class, 'getList']);
    Route::post('/', [CarController::class, 'update']);
    Route::get('/{id}', [CarController::class, 'show']);
});
