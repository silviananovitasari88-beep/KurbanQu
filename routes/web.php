<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// Route untuk Halaman Depan / Dashboard Warga (yang kemarin)
Route::get('/', function () {
    return view('kurban.home');
});

// Route untuk Halaman Dashboard Utama Admin Kurban
Route::get('/admin', function () {
    return view('admin.dashboard');
});                                         
//  return view('admin.dashboard');