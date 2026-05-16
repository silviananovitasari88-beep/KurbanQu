<?php

use Illuminate\Support\Facades\Route;

// Route untuk Halaman Depan / Dashboard Warga (yang kemarin)
Route::get('/', function () {
    return view('kurban.home');
});

// Route untuk Halaman Login Admin Kurban
Route::get('/admin/login', function () {
    return view('admin.login'); 
    // Ini otomatis memanggil resources/views/admin/login.blade.php
});

// Route untuk Halaman Dashboard Utama Admin Kurban
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard'); 
    // Ini otomatis memanggil resources/views/admin/dashboard.blade.php
});