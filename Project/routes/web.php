<?php

use App\Http\Controllers\WorkerRegistrationController;
use App\Http\Controllers\browseWorkRequestController;
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



Route::get('/browseWorkRequest', [browseWorkRequestController::class, 'index'])->name('browse.work.requests.index');

Route::get('/requests/{request}', [BrowseWorkRequestController::class, 'show'])->name('work_requests.show');
