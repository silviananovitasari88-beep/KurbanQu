<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WargaQrController;
use App\Models\Warga;

// Halaman utama penerima
Route::get('/', function () {
    return view('kurban.home');
});

// Download QR Code penerima
Route::post('/warga/download-qr', [WargaQrController::class, 'download'])->name('warga.download-qr');
Route::post('/warga/qr/download',  [WargaQrController::class, 'download']); // alias lama

// Halaman admin
Route::get('/admin', function () {
    return view('admin.dashboard');
});

// API admin
Route::get('/admin/api/distribusi/snapshot',          [AdminController::class, 'distribusiSnapshot']);
Route::post('/admin/api/distribusi/{idStok}/manual',  [AdminController::class, 'updateDistribusiManual']);
Route::delete('/admin/api/import-temp',               [AdminController::class, 'deleteTempImport']);
Route::delete('/admin/api/penerima',                  [AdminController::class, 'clearPenerimaData']);
Route::delete('/admin/api/penerima/clear-all',        [AdminController::class, 'clearPenerimaData']);

// Simpan penerima dari upload CSV admin → warga + distribusi
Route::post('/simpan-penerima', function (\Illuminate\Http\Request $request) {

    $request->validate([
        'penerima'        => 'required|array|min:1',
        'penerima.*.nkk'  => 'required|string|min:6',
        'penerima.*.nama' => 'required|string|min:1',
    ]);

    $penerima = $request->input('penerima', []);
    $mode     = $request->input('mode', 'append');

    try {
        DB::beginTransaction();

        // Handle replace: hapus distribusi dulu (FK), baru warga
        if ($mode === 'replace') {
            DB::table('distribusi')->delete();
            DB::table('warga')->delete();
        }

        $created = 0;
        $updated = 0;
        $failed  = 0;
        $errors  = [];
        $nextId  = (int)(DB::table('warga')->max('id_penerima') ?? 0) + 1;

        foreach ($penerima as $idx => $row) {
            try {
                $nkk  = preg_replace('/\D/', '', $row['nkk']  ?? '');
                $nama = trim($row['nama'] ?? '');

                if (strlen($nkk) < 6) {
                    $errors[] = "Baris " . ($idx + 1) . ": No KK terlalu pendek ({$nkk})";
                    $failed++;
                    continue;
                }
                if (strlen($nama) < 1) {
                    $errors[] = "Baris " . ($idx + 1) . ": Nama kosong";
                    $failed++;
                    continue;
                }

                $exists = DB::table('warga')->where('no_kk', $nkk)->exists();

                if ($exists) {
                    // Update data warga
                    DB::table('warga')->where('no_kk', $nkk)->update([
                        'nama_kk' => $nama,
                        'alamat'  => trim($row['alamat']  ?? ''),
                        'no_telp' => trim($row['notelp']  ?? ''),
                    ]);
                    $updated++;

                } else {
                    // Insert warga baru (QR_id_qr nullable)
                    $idPenerima = $nextId++;
                    DB::table('warga')->insert([
                        'no_kk'       => $nkk,
                        'nama_kk'     => $nama,
                        'alamat'      => trim($row['alamat']  ?? ''),
                        'no_telp'     => trim($row['notelp']  ?? ''),
                        'id_penerima' => $idPenerima,
                        'QR_id_qr'    => null,
                    ]);

                    // Auto-insert ke distribusi
                    $distExists = DB::table('distribusi')->where('warga_no_kk', $nkk)->exists();
                    if (!$distExists) {
                        DB::table('distribusi')->insert([
                            'warga_no_kk'     => $nkk,
                            'QR_id_qr'        => null,
                            'dowload_qr'      => 'Belum',
                            'st_pengambilan'  => 'pending',
                            'mtd_pengambilan' => null,
                        ]);
                    }

                    $created++;
                }

            } catch (\Exception $e) {
                $errors[] = "Baris " . ($idx + 1) . ": " . $e->getMessage();
                $failed++;
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "✅ {$created} penerima baru, {$updated} diperbarui" . ($failed > 0 ? ", {$failed} gagal" : ''),
            'data'    => compact('created', 'updated', 'failed', 'errors'),
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => '❌ Gagal menyimpan: ' . $e->getMessage(),
        ], 422);
    }
});