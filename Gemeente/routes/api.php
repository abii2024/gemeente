<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ComplaintApiController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Api\StatisticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API routes with rate limiting
Route::middleware(['throttle:api'])->group(function () {

    // Complaints API
    Route::prefix('complaints')->group(function () {
        Route::get('/', [ComplaintApiController::class, 'index']); // GET /api/complaints?status=open&priority=high&limit=10
        Route::post('/', [ComplaintApiController::class, 'store']); // POST /api/complaints
        Route::get('/search', [ComplaintApiController::class, 'search']); // GET /api/complaints/search?q=term
        Route::get('/map', [ComplaintApiController::class, 'mapData']); // GET /api/complaints/map?status=open
        Route::get('/{complaint}', [ComplaintApiController::class, 'show']); // GET /api/complaints/{id}
        Route::patch('/{complaint}/status', [ComplaintApiController::class, 'updateStatus']); // PATCH /api/complaints/{id}/status
        Route::post('/{complaint}/notes', [ComplaintApiController::class, 'addNote']); // POST /api/complaints/{id}/notes
    });

    // Search API
    Route::get('/search', [SearchApiController::class, 'search']); // GET /api/search?id=123

    // Statistics API
    Route::get('/statistics', [StatisticsController::class, 'index']); // GET /api/statistics?period=month

    // Chatbot API
    Route::prefix('chat')->group(function () {
        Route::post('/', [ChatController::class, 'chat']); // POST /api/chat
        Route::get('/welcome', [ChatController::class, 'welcome']); // GET /api/chat/welcome
        Route::get('/faq', [ChatController::class, 'faq']); // GET /api/chat/faq
    });

});
