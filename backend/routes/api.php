<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'platform' => 'Lernyxa LMS API',
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [\App\Http\Controllers\Api\v1\AuthController::class, 'login']);
        Route::post('/register', [\App\Http\Controllers\Api\v1\AuthController::class, 'register']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [\App\Http\Controllers\Api\v1\AuthController::class, 'me']);
            Route::post('/logout', [\App\Http\Controllers\Api\v1\AuthController::class, 'logout']);
        });
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
