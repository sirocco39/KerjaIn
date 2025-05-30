<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/verify-email', fn () => view('auth.verify-email'))->name('verification.notice');
Route::post('/verify-email', [App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])->name('verification.verify');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store');

Route::get('/login/otp', [AuthenticatedSessionController::class, 'showOtpForm'])
    ->name('login.otp');

Route::post('/login/otp', [AuthenticatedSessionController::class, 'verifyOtp'])
    ->name('login.otp');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
