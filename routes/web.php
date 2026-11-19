<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WargaQrController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\PeriodeController;

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
// [PERIODE] hapus warga, daftar penerima, snapshot distribusi, hapus semua penerima kini lewat PeriodeController
Route::delete('/admin/api/warga/{noKk}', [PeriodeController::class, 'deleteWarga'])
    ->middleware('auth')
    ->name('admin.delete-warga');

Route::get('/admin/api/penerima/list', [PeriodeController::class, 'getPenerimaList'])
    ->middleware('auth');

Route::get('/admin/api/distribusi/snapshot', [PeriodeController::class, 'distribusiSnapshot']);
Route::post('/admin/api/distribusi/{idStok}/manual', [AdminController::class, 'updateDistribusiManual']);
Route::post('/admin/api/distribusi/{idStok}/batalkan', [AdminController::class, 'batalkanDistribusi'])
    ->middleware('auth');

Route::delete('/admin/api/import-temp', [AdminController::class, 'deleteTempImport']);
Route::delete('/admin/api/penerima', [PeriodeController::class, 'clearPenerimaData'])->middleware('auth');
Route::delete('/admin/api/penerima/clear-all', [PeriodeController::class, 'clearPenerimaData'])->middleware('auth');

Route::post('/simpan-penerima', [PeriodeController::class, 'simpanPenerima'])->middleware('auth');

// ---- Tracking ----
Route::get('/api/tracking', [TrackingController::class, 'getSteps']);
Route::post('/admin/api/tracking/{urutan}', [TrackingController::class, 'updateStep'])
    ->middleware('auth');
Route::post('/admin/api/tracking/reset', [TrackingController::class, 'reset'])
    ->middleware('auth');

// ---- Hewan & Mudhohi ----
Route::get('/admin/api/hewan', [PeriodeController::class, 'getHewan']);
Route::post('/admin/api/hewan', [PeriodeController::class, 'storeHewan'])->middleware('auth');
Route::delete('/admin/api/hewan/{idHewan}', [PeriodeController::class, 'deleteHewan'])->middleware('auth');

Route::get('/admin/api/mudhohi', [PeriodeController::class, 'getMudhohi']);
Route::post('/admin/api/mudhohi', [AdminController::class, 'storeMudhohi']);
Route::delete('/admin/api/mudhohi/{idMudhohi}', [AdminController::class, 'deleteMudhohi']);

// ---- [PERIODE] Rekap tahunan, periode baru, riwayat perubahan ----
Route::get('/admin/api/rekap-tahunan', [PeriodeController::class, 'rekapTahunan'])->middleware('auth');
Route::post('/admin/api/periode/mulai', [PeriodeController::class, 'mulaiPeriodeBaru'])->middleware('auth');
Route::get('/admin/api/audit-log', [PeriodeController::class, 'daftarAudit'])->middleware('auth');

// ---- Settings & Validation ----
Route::get('/api/settings', [AdminController::class, 'getSettings']);
Route::post('/admin/api/settings', [AdminController::class, 'saveSettings'])->middleware('auth');
Route::get('/api/scan-validation', [AdminController::class, 'getQrScanValidation']);
Route::get('/admin/api/scan/{qrCode}', [AdminController::class, 'scanByQrCode'])->middleware('auth');

// ---- Stok Daging Kurban ----
Route::get('/api/stok', [AdminController::class, 'getStok']);
Route::get('/admin/api/stok', [AdminController::class, 'getStok'])->middleware('auth');
Route::post('/admin/api/stok/tambah', [AdminController::class, 'tambahStok'])->middleware('auth');
Route::post('/admin/api/stok/atur', [AdminController::class, 'aturStok'])->middleware('auth');

Route::get('/api/hewan', [PeriodeController::class, 'getHewan']);
Route::get('/api/mudhohi', [PeriodeController::class, 'getMudhohi']);
