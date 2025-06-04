<?php

use App\Http\Controllers\RequestController;
use App\Models\Request;
use Illuminate\Support\Facades\Route;

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
    return view('Job_Requester.dummy-job_req-tawarkankerja');
});

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

Route::get('/job-taker/beranda', function () {
    return view('Job_Taker.dummy-job_taker-beranda');
});

Route::get('/job-taker/cari-kerja', function () {
    return view('Job_Taker.dummy-job_taker-carikerja');
});

Route::get('/job-taker/pesan', function () {
    return view('Job_Taker.dummy-job_taker-pesan');
});

Route::get('/job-taker/riwayat', function () {
    return view('Job_Taker.dummy-job_taker-riwayat');
});

// Route::get('/postwork', function () {
//     return view('postwork');
// });

Route::get('/navbar-job_taker', function () {
    return view('Master.master-job_taker');
});

Route::get('/navbar-job_req', function () {
    return view('Master.master-job_req');
});
