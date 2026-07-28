<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WargaQrController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrackingController;

// ---- Auth ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/auth/login',    [AuthController::class, 'login'])->name('auth.do-login');
    Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
});

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');

// ---- Admin (protected) ----
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [AdminController::class, 'dashboard']); // redirect ke dashboard
});

// ---- Halaman utama ----
Route::get('/', [HomeController::class, 'index'])->name('home');

// ---- Warga QR & Login ----
Route::post('/warga/download-qr', [WargaQrController::class, 'download'])->name('warga.download-qr');
Route::post('/warga/login', [WargaQrController::class, 'login'])->name('warga.login');
Route::get('/warga/status', [WargaQrController::class, 'status'])->name('warga.status');

// ---- API Admin (distribusi, warga, dll) ----
Route::delete('/admin/api/warga/{noKk}', [AdminController::class, 'deleteWarga'])
    ->middleware('auth')
    ->name('admin.delete-warga');

Route::get('/admin/api/penerima/list', [AdminController::class, 'getPenerimaList'])
    ->middleware('auth');

Route::get('/admin/api/distribusi/snapshot', [AdminController::class, 'distribusiSnapshot']);
Route::post('/admin/api/distribusi/{idStok}/manual', [AdminController::class, 'updateDistribusiManual']);
Route::post('/admin/api/distribusi/{idStok}/batalkan', [AdminController::class, 'batalkanDistribusi'])
    ->middleware('auth');

Route::delete('/admin/api/import-temp', [AdminController::class, 'deleteTempImport']);
Route::delete('/admin/api/penerima', [AdminController::class, 'clearPenerimaData']);
Route::delete('/admin/api/penerima/clear-all', [AdminController::class, 'clearPenerimaData']);

Route::post('/simpan-penerima', [PenerimaController::class, 'simpanPenerima']);

// ---- Tracking ----
Route::get('/api/tracking', [TrackingController::class, 'getSteps']);
Route::post('/admin/api/tracking/{urutan}', [TrackingController::class, 'updateStep'])
    ->middleware('auth');
Route::post('/admin/api/tracking/reset', [TrackingController::class, 'reset'])
    ->middleware('auth');

// ---- Hewan & Mudhohi ----
Route::get('/admin/api/hewan', [AdminController::class, 'getHewan']);
Route::post('/admin/api/hewan', [AdminController::class, 'storeHewan']);
Route::delete('/admin/api/hewan/{idHewan}', [AdminController::class, 'deleteHewan']);

Route::get('/admin/api/mudhohi', [AdminController::class, 'getMudhohi']);
Route::post('/admin/api/mudhohi', [AdminController::class, 'storeMudhohi']);
Route::delete('/admin/api/mudhohi/{idMudhohi}', [AdminController::class, 'deleteMudhohi']);

Route::get('/api/hewan', [AdminController::class, 'getHewan']);
Route::get('/api/mudhohi', [AdminController::class, 'getMudhohi']);