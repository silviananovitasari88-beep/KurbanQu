<?php

use Illuminate\Support\Facades\Route;

// Halaman Utama QurbanQu
Route::get('/', function () {
    return view('kurban.home'); 
});