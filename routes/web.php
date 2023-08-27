<?php

use App\Http\Controllers\Api\CarController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/login', function () {
    return response()->json('Обратитесь к админитсрации сайта', 401);
})->name('login');

Route::view('/', 'welcome');
