<?php

use App\Http\Controllers\Admin\ComplaintAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DatabaseController;
use App\Http\Controllers\Admin\NoteController;
use Illuminate\Support\Facades\Route;

// All admin routes require authentication and admin access
Route::middleware(['auth', 'admin', 'noindex', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

});
