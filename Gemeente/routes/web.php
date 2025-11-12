<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\ComplaintController;
use App\Http\Controllers\Web\UploadController;
use App\Http\Controllers\DienstenController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::view('/', 'welcome')->name('home');

// Complaint routes (public)
Route::get('/klacht/indienen', [ComplaintController::class, 'create'])->name('complaint.create');
Route::post('/klacht/indienen', [ComplaintController::class, 'store'])->name('complaint.store');
Route::get('/klacht/bedankt', [ComplaintController::class, 'thanks'])->name('complaint.thanks');
Route::get('/klacht/zoeken', function () {
    return view('pages.complaint-search');
})->name('complaint.search');
Route::get('/klacht/track', [ComplaintController::class, 'track'])->name('complaint.track');

// Upload routes (public for complaint attachments)
Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
Route::delete('/upload', [UploadController::class, 'delete'])->name('upload.delete');

// Auth routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Diensten routes
    Route::get('/diensten/paspoort', [DienstenController::class, 'paspoort'])->name('diensten.paspoort');
    Route::get('/diensten/rijbewijs', [DienstenController::class, 'rijbewijs'])->name('diensten.rijbewijs');
    Route::get('/diensten/vergunning', [DienstenController::class, 'vergunning'])->name('diensten.vergunning');
    Route::get('/diensten/parkeren', [DienstenController::class, 'parkeren'])->name('diensten.parkeren');
    Route::get('/diensten/subsidie', [DienstenController::class, 'subsidie'])->name('diensten.subsidie');
    Route::post('/diensten/afspraak', [DienstenController::class, 'storeAfspraak'])->name('diensten.afspraak.store');
});

require __DIR__.'/auth.php';

// Include admin routes
require __DIR__.'/admin.php';
