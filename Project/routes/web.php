<?php

use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/postwork', function () {
    return view('postwork');
});

Route::post('/postwork', [RequestController::class, 'add']);
