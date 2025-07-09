<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);

Route::middleware('auth.api_token')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
