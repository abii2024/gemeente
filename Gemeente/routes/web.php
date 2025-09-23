<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\ComplaintController;
use App\Http\Controllers\Web\UploadController;
use App\Http\Controllers\Web\PageController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::view('/', 'welcome')->name('home');

// Complaint routes (public)
Route::get('/klacht/indienen', [ComplaintController::class, 'create'])->name('complaint.create');
Route::post('/klacht/indienen', [ComplaintController::class, 'store'])->name('complaint.store');
Route::get('/klacht/bedankt', [ComplaintController::class, 'thanks'])->name('complaint.thanks');

// Upload routes (public for complaint attachments)
Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
Route::delete('/upload', [UploadController::class, 'delete'])->name('upload.delete');

// Auth routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Include admin routes
require __DIR__.'/admin.php';
