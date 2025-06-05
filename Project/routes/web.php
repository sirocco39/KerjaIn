<?php

use App\Http\Controllers\WorkerRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return view('Job_Requester.dummy-job_req-landingpage');
});

Route::get('/job-req/beranda', function(){
    return view('Job_Requester.dummy-job_req-beranda');
});

Route::get('/job-req/tawarkan-kerja', function(){
    return view('Job_Requester.dummy-job_req-tawarkankerja');
});

Route::get('/job-req/pesan', function(){
    return view('Job_Requester.dummy-job_req-pesan');
});

Route::get('/job-req/riwayat', function(){
    return view('Job_Requester.dummy-job_req-riwayat');
});



Route::get('/job_taker', function(){
    return view('Job_Taker.dummy-job_taker-landingpage');
});

Route::get('/joinn', function () {
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
Route::get('/job-taker/beranda', function(){
    return view('Job_Taker.dummy-job_taker-beranda');
});

Route::get('/job-taker/cari-kerja', function(){
    return view('Job_Taker.dummy-job_taker-carikerja');
});

Route::get('/job-taker/pesan', function(){
    return view('Job_Taker.dummy-job_taker-pesan');
});

Route::get('/job-taker/riwayat', function(){
    return view('Job_Taker.dummy-job_taker-riwayat');
});



Route::get('/navbar-job_taker', function () {
    return view('Master.master-job_taker');
});

Route::get('/navbar-job_req', function(){
    return view('Master.master-job_req');
});

