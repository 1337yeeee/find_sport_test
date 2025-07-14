<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/register', [UserController::class, 'register'])->middleware('api');

Route::middleware('auth.api_token')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
