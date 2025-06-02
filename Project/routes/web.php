<?php

use App\Http\Controllers\WorkerRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('joinWorker.joinn');
});

// Route::get('/', function () {
//     return redirect()->route('worker.register.step1');
// });

// Grup route untuk pendaftaran pekerja tanpa autentikasi
Route::prefix('joinWorker')->name('worker.register.')->group(function () {
    // Langkah 1: Data Pribadi (Form GET, Proses POST)
    // URL: /joinWorker/join
    Route::get('/join', [WorkerRegistrationController::class, 'createStep1'])->name('step1');
    Route::post('/join', [WorkerRegistrationController::class, 'store1'])->name('store1'); // <-- KEMBALIKAN KE 'store1'

    // Langkah 2: Detail Kontrak (Form GET, Proses POST)
    // URL: /joinWorker/join2
    Route::get('/join2', [WorkerRegistrationController::class, 'createStep2'])->name('step2');
    Route::post('/join2', [WorkerRegistrationController::class, 'store2'])->name('store2');

    // Langkah 3: Verifikasi / Upload Dokumen / Finalisasi (Form GET, Proses POST)
    // URL: /joinWorker/join3
    Route::get('/join3', [WorkerRegistrationController::class, 'createStep3'])->name('step3');
    Route::post('/join3', [WorkerRegistrationController::class, 'finalizeRegistration'])->name('finalize');

    // Halaman Sukses
    // URL: /joinWorker/success
    Route::get('/success', [WorkerRegistrationController::class, 'showSuccessPage'])->name('success');
});