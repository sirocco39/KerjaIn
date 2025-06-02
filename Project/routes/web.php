<?php

use App\Http\Controllers\RequestController;
use App\Models\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/postwork', function () {
    return view('postwork');
});

Route::post('/postwork', [RequestController::class, 'add']);

Route::get('/request/{request:slug}', function (Request $request) {
    return view('request', ['workRequest' => $request]);
});
