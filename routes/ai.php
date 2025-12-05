<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiController;
use App\Http\Controllers\Api\AiApiController;

/*
|--------------------------------------------------------------------------
| AI Routes
|--------------------------------------------------------------------------
*/

// Admin AI Pages
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/ai-studio', [AiController::class, 'studio'])->name('ai.studio');
    Route::get('/content-writer', [AiController::class, 'contentWriter'])->name('ai.content-writer');
    Route::get('/ai-logs', [AiController::class, 'logs'])->name('ai.logs');
});

// API Routes for AI
Route::prefix('api/ai')->name('api.ai.')->middleware(['throttle:ai'])->group(function () {
    Route::post('/run', [AiApiController::class, 'run'])->name('run');
    Route::post('/chat', [AiApiController::class, 'chat'])->name('chat');
    Route::post('/summarize', [AiApiController::class, 'summarize'])->name('summarize');
    Route::post('/title', [AiApiController::class, 'generateTitle'])->name('title');
    Route::post('/blog', [AiApiController::class, 'generateBlog'])->name('blog');
    Route::post('/translate', [AiApiController::class, 'translate'])->name('translate');
    Route::post('/sentiment', [AiApiController::class, 'sentiment'])->name('sentiment');
});
