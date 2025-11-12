<?php

use App\Http\Controllers\Admin\ComplaintAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DatabaseController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// All admin routes require authentication and admin access
Route::middleware(['auth', 'admin', 'noindex', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard API endpoints
    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/recent-complaints', [DashboardController::class, 'recentComplaints'])->name('recent');
        Route::get('/search', [DashboardController::class, 'searchById'])->name('search');
        Route::get('/map-data', [DashboardController::class, 'mapData'])->name('map-data');
        Route::get('/complaint/{id}', [DashboardController::class, 'complaintDetails'])->name('details');
        Route::post('/filter', [DashboardController::class, 'filter'])->name('filter');
    });

    // Database management
    Route::get('/database', [DatabaseController::class, 'index'])->name('database.index');
    Route::get('/database/{table}', [DatabaseController::class, 'table'])->name('database.table');

    // Complaints management
    Route::prefix('complaints')->name('complaints.')->group(function () {
        Route::get('/', [ComplaintAdminController::class, 'index'])->name('index');
        Route::get('/map', [ComplaintAdminController::class, 'map'])->name('map');
        Route::get('/{complaint}', [ComplaintAdminController::class, 'show'])->name('show');
        Route::get('/{complaint}/edit', [ComplaintAdminController::class, 'edit'])->name('edit');
        Route::patch('/{complaint}', [ComplaintAdminController::class, 'update'])->name('update');
        Route::patch('/{complaint}/status', [ComplaintAdminController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{complaint}', [ComplaintAdminController::class, 'destroy'])->name('destroy');

        // Notes for complaints
        Route::post('/{complaint}/notes', [NoteController::class, 'store'])->name('notes.store');
        Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
    });

    // User management
    Route::resource('users', UserController::class);

});
