<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CvController;
use App\Http\Controllers\Api\JobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RESTful API for Job Portal & CV Builder
| Base URL: /api
|
*/

// ================================================================
// PUBLIC ROUTES (no authentication required)
// ================================================================

// C-2: throttle public reads to mitigate scraping/DoS
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/{jobPost}', [JobController::class, 'show']);
});

// ================================================================
// AUTH ROUTES (C-2: throttle + rate-limit login/register)
// ================================================================

Route::prefix('auth')->group(function () {
    // 5 lần/phút cho login: chống brute force + enumeration
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });
});

// ================================================================
// PROTECTED ROUTES (authentication required via Sanctum)
// ================================================================

Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {

    // -------------------- Auth --------------------
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
    });

    // -------------------- CVs --------------------
    Route::apiResource('cvs', CvController::class);

    // -------------------- Applications --------------------
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::post('/applications', [ApplicationController::class, 'store']);
    Route::get('/applications/{application}', [ApplicationController::class, 'show']);

    // -------------------- Admin / HR Routes (H-2: throttled) --------------------
    // 60 req/min cho admin thao tác; thắt thêm 'role:admin|hr' middleware
    // nếu muốn chặn user thường truy cập.
    Route::middleware('throttle:60,1')->prefix('admin')->group(function () {
        Route::get('/jobs', [JobController::class, 'adminIndex']);
        Route::post('/jobs', [JobController::class, 'store']);
        Route::put('/jobs/{jobPost}', [JobController::class, 'update']);
        Route::delete('/jobs/{jobPost}', [JobController::class, 'destroy']);

        Route::get('/jobs/{jobPost}/applications', [ApplicationController::class, 'applicantsByJob']);
        Route::get('/applications', [ApplicationController::class, 'adminIndex']);
        Route::put('/applications/{application}/status', [ApplicationController::class, 'updateStatus']);
        Route::delete('/applications/{application}', [ApplicationController::class, 'destroy']);
    });
});
