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

    // Authenticated Cohort & Curriculum Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Cohorts
        Route::get('/cohorts', [\App\Http\Controllers\Api\v1\CohortController::class, 'index']);
        Route::post('/cohorts', [\App\Http\Controllers\Api\v1\CohortController::class, 'store']);
        Route::get('/cohorts/my', [\App\Http\Controllers\Api\v1\CohortController::class, 'myCohorts']);
        Route::get('/cohorts/{id}', [\App\Http\Controllers\Api\v1\CohortController::class, 'show']);
        Route::put('/cohorts/{id}', [\App\Http\Controllers\Api\v1\CohortController::class, 'update']);
        Route::post('/cohorts/{id}/enroll', [\App\Http\Controllers\Api\v1\CohortController::class, 'enroll']);

        // Courses
        Route::get('/courses', [\App\Http\Controllers\Api\v1\CourseController::class, 'index']);
        Route::post('/courses', [\App\Http\Controllers\Api\v1\CourseController::class, 'store']);
        Route::get('/courses/{id}', [\App\Http\Controllers\Api\v1\CourseController::class, 'show']);
        Route::put('/courses/{id}', [\App\Http\Controllers\Api\v1\CourseController::class, 'update']);
        Route::post('/courses/{id}/attach-cohort', [\App\Http\Controllers\Api\v1\CourseController::class, 'attachToCohort']);

        // Modules & Lessons
        Route::post('/modules', [\App\Http\Controllers\Api\v1\CurriculumController::class, 'storeModule']);
        Route::post('/lessons', [\App\Http\Controllers\Api\v1\CurriculumController::class, 'storeLesson']);
        Route::get('/lessons/{id}', [\App\Http\Controllers\Api\v1\CurriculumController::class, 'showLesson']);
        Route::post('/lessons/{id}/complete', [\App\Http\Controllers\Api\v1\CurriculumController::class, 'completeLesson']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
