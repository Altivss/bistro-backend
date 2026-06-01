<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;

Route::middleware('api')->group(function () {
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
