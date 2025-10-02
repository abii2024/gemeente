<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ComplaintApiController;
use App\Http\Controllers\Api\SearchApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API routes with rate limiting
Route::middleware(['throttle:api'])->group(function () {

    // Complaints API
    Route::prefix('complaints')->group(function () {
        Route::get('/', [ComplaintApiController::class, 'index']); // GET /api/complaints?recent=5
        Route::get('/{complaint}', [ComplaintApiController::class, 'show']); // GET /api/complaints/{id}
    });

    // Search API
    Route::get('/search', [SearchApiController::class, 'search']); // GET /api/search?id=123

    // Chatbot API
    Route::prefix('chat')->group(function () {
        Route::post('/', [ChatController::class, 'chat']); // POST /api/chat
        Route::get('/welcome', [ChatController::class, 'welcome']); // GET /api/chat/welcome
        Route::get('/faq', [ChatController::class, 'faq']); // GET /api/chat/faq
    });

});
