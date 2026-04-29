<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KosController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\KosanController;

// Home routes
Route::get('welcome', function () {
    return view('welcome');
});

Route::get('/', [KosanController::class, 'index'])->name('home');

// Booking submission route (public)
Route::post('/bookings/create', [BookingController::class, 'storeFromWeb'])->name('bookings.create-web');

// ============ ADMIN AUTH ROUTES ============
Route::get('admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminController::class, 'login'])->name('admin.login.post');

// ============ ADMIN PROTECTED ROUTES ============
Route::group(['prefix' => 'admin', 'middleware' => 'auth.admin'], function () {
    Route::match(['get', 'post'], '', [AdminController::class, 'dashboard']);
    Route::match(['get', 'post'], 'dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::post('settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

    // ============ KOS (ROOMS) ROUTES ============
    Route::get('rooms', [KosController::class, 'index'])->name('rooms.index');
    Route::post('rooms', [KosController::class, 'store'])->name('rooms.store');
    Route::put('rooms/{kos}', [KosController::class, 'update'])->name('rooms.update');
    Route::delete('rooms/{kos}', [KosController::class, 'destroy'])->name('rooms.destroy');

    // ============ BOOKING ROUTES ============
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::put('bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // ============ ADMIN AUTH ROUTES ============
});