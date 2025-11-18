<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasirAuthController;
use App\Http\Middleware\EnsureUserIsKasir; 
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('dashboard');
});

require __DIR__.'/auth.php';
