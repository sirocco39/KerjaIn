<?php

use App\Http\Controllers\WorkerRegistrationController;
use App\Http\Controllers\browseWorkRequestController;
use App\Http\Controllers\RequestController;
use App\Models\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;       
use App\Http\Controllers\PusherController;

// Route on-going-work-request
Route::get('/job-req/on-going-work-request', function() {
    return view('Job_Requester.on-going-work-request');});

Route::post('/send-otp', [RegisteredUserController::class, 'sendOtp'])->name('send.otp');

Route::get('/auth-google-redirect', [SocialController::class, 'google_redirect'])->name('auth-google-redirect');
Route::get('/auth-google-callback', [SocialController::class, 'google_callback'])->name('auth-google-callback');

Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


Route::get('/', function () {
    return view('Job_Requester.dummy-job_req-landingpage');
});


Route::resource('requesttt', RequestController::class);

Route::get('/job-req/beranda', function () {
    //get five latest open requests and deleted_at is null
    $fiveLatestRequests = Request::whereNull('deleted_at')
        ->where('status', 'open')
        ->latest()
        ->take(5)
        ->get();
    return view('Job_Requester.beranda', compact('fiveLatestRequests'));
});

Route::get('/job-req/tawarkan-kerja', function () {
    return view('Job_Requester.postwork');
});

// Route::get('/job-req/pesan', function () {
Route::post('/job-req/tawarkan-kerja', [RequestController::class, 'add']);

Route::post('/postwork', [RequestController::class, 'add']);

Route::get('/request/{request:slug}', function (Request $request) {
    return view('request', ['workRequest' => $request]);
});

Route::get('/edit/{request:slug}', function (Request $request) {
    return view('edit', ['workRequest' => $request]);
});

Route::get('/job-req/pesan', function () {
    return view('Job_Requester.dummy-job_req-pesan');
});


Route::get('/job-req/riwayat', function () {
    return view('Job_Requester.dummy-job_req-riwayat');
});



Route::get('/job_taker', function () {
    return view('Job_Taker.dummy-job_taker-landingpage');
});

Route::get('/joinworker', function () {
    return redirect()->route('worker.register.step1');
});

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
    Route::get('/pending', [WorkerRegistrationController::class, 'showPendingPage'])->name('pending');
});
Route::get('/job-taker/beranda', function(){
    return view('Job_Taker.dummy-job_taker-beranda');
});

Route::get('/job-taker/cari-kerja', [browseWorkRequestController::class, 'index'])->name('browse.work.requests.index');
Route::get('/job-taker/cari-kerja', function () {
    return view('Job_Taker.dummy-job_taker-carikerja');
});


Route::get('/job-taker/pesan', function () {
    return view('Job_Taker.dummy-job_taker-pesan');
});

Route::get('/job-taker/riwayat', function () {
    return view('Job_Taker.dummy-job_taker-riwayat');
});


Route::get('/navbar-job_taker', function () {
    return view('Master.master-job_taker');
});

Route::get('/navbar-job_req', function () {
    return view('Master.master-job_req');
});


Route::get('/requests/{request}', [BrowseWorkRequestController::class, 'show'])->name('work_requests.show');

Route::get('/', function () {
    return view('landing');
});

