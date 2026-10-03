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
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
