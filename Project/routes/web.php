<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;   // ✅ correct import
use App\Http\Controllers\ProfileController;

Route::post('/send-otp', [RegisteredUserController::class, 'sendOtp'])
     ->name('send.otp');                                  // ✅ placed early for clarity

require __DIR__.'/auth.php';                              // Breeze routes

Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/send-otp', [RegisteredUserController::class, 'sendOtp'])->name('send.otp');