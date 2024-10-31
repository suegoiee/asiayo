<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\Log403Requests;
use App\Http\Controllers\TestController;

Route::get('/', function () {
    return view('welcome');
});