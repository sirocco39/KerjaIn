<?php

use App\Http\Controllers\WorkerRegistrationController;
use App\Http\Controllers\browseWorkRequestController;
use Illuminate\Support\Facades\Route;

// Route default yang mengarahkan ke langkah 1 pendaftaran pekerja
// Ini akan mengarahkan '/' ke '/joinWorker/join'
Route::get('/', function () {
    return view('welcome');
});

Route::get('/pesan', action:function(){
    return view('dummypesan');
});

Route::get('/riwayat', function () {
    return view('dummyriwayat');
});

Route::get('/browseWorkRequest', [browseWorkRequestController::class, 'index'])->name('browse.work.requests.index');

Route::get('/requests/{request}', [BrowseWorkRequestController::class, 'show'])->name('work_requests.show');