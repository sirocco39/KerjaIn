<?php

use App\Http\Controllers\RequestController;
use App\Models\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dummylandingpage');
});

Route::get('/navbar', function () {
    return view('master');
});

Route::get('/beranda', function () {
    return view('dummyberanda');
});

Route::get('/cari-kerja', function () {
    return view('dummycarikerja');
});

Route::get('/pesan', action:function(){
    return view('dummypesan');
});

Route::get('/riwayat', function () {
    return view('dummyriwayat');
});

Route::get('/postwork', function () {
    return view('postwork');
});

Route::post('/postwork', [RequestController::class, 'add']);

Route::get('/request/{request:slug}', function (Request $request) {
    return view('request', ['workRequest' => $request]);
});
