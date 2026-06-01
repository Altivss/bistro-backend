<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AuthController;

// Handle preflight requests
Route::options('/{any}', function() {
    return response()->noContent();
})->where('any', '.*');

Route::middleware('api')->group(function () {
    // Auth endpoints (public)
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    // Feedback endpoints
    Route::post('/feedback', [FeedbackController::class, 'store']);
    Route::get('/feedback', [FeedbackController::class, 'index']);
    Route::get('/feedback/{id}', [FeedbackController::class, 'show']);
    Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy']);
    Route::patch('/feedback/{id}/archive', [FeedbackController::class, 'archive']);
    Route::patch('/feedback/{id}/status', [FeedbackController::class, 'updateStatus']);
    Route::post('/feedback/{id}/response', [FeedbackController::class, 'addResponse']);
    Route::patch('/feedback/{id}/response', [FeedbackController::class, 'updateResponse']);
});
