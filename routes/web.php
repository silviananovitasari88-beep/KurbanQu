<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WargaQrController;
use App\Models\Warga;

// Route untuk Halaman Depan / Dashboard Warga (yang kemarin)
Route::get('/', function () {
    return view('kurban.home');
});

// qr download
Route::post('/warga/download-qr', [WargaQrController::class, 'download'])
    ->name('warga.download-qr');

// Route untuk Halaman Dashboard Utama Admin Kurban
Route::get('/admin', function () {
    return view('admin.dashboard');
});                                         
//  return view('admin.dashboard');

Route::get('/admin/api/distribusi/snapshot', [AdminController::class, 'distribusiSnapshot']);
Route::post('/admin/api/distribusi/{idStok}/manual', [AdminController::class, 'updateDistribusiManual']);
Route::delete('/admin/api/import-temp', [AdminController::class, 'deleteTempImport']);
Route::delete('/admin/api/penerima', [AdminController::class, 'clearPenerimaData']);
Route::delete('/admin/api/penerima/clear-all', [AdminController::class, 'clearPenerimaData']);

Route::post('/warga/qr/download', [WargaQrController::class, 'download']);

Route::post('/simpan-penerima', function(\Illuminate\Http\Request $request) {
    // ✅ Validasi input
    $request->validate([
        'penerima' => 'required|array|min:1',
        'penerima.*.nkk' => 'required|string|min:6',
        'penerima.*.nama' => 'required|string|min:2',
    ], [
        'penerima.required' => 'Data penerima tidak boleh kosong',
        'penerima.*.nkk.required' => 'No KK wajib diisi',
        'penerima.*.nama.required' => 'Nama wajib diisi',
    ]);
    
    $penerima = $request->input('penerima', []);
    $mode = $request->input('mode', 'append');
    
    try {
        DB::beginTransaction();
        
        // ✅ Handle replace mode
        if ($mode === 'replace') {
            Warga::truncate();
        }
        
        $created = 0;
        $updated = 0;
        $failed = 0;
        $errors = [];
        $nextId = 1;
        
        // Get max id_penerima
        $maxId = Warga::max('id_penerima') ?? 0;
        $nextId = $maxId + 1;
        
        foreach ($penerima as $idx => $row) {
            try {
                // ✅ Normalisasi No KK (hanya digit)
                $nkk = preg_replace('/\D/', '', $row['nkk'] ?? '');
                $nama = trim($row['nama'] ?? '');
                
                // ✅ Validasi
                if (strlen($nkk) < 10) {
                    $errors[] = "Baris " . ($idx + 1) . ": No KK '{$row['nkk']}' kurang dari 10 digit";
                    $failed++;
                    continue;
                }
                
                if (strlen($nama) < 2) {
                    $errors[] = "Baris " . ($idx + 1) . ": Nama tidak boleh kosong";
                    $failed++;
                    continue;
                }
                
                // ✅ Update atau Create dengan semua kolom - GUNAKAN RAW SQL
                $exists = DB::table('warga')->where('no_kk', $nkk)->exists();
                
                if ($exists) {
                    // Update existing record
                    $updateData = [
                        'nama_kk'     => $nama,
                        'alamat'      => trim($row['alamat'] ?? ''),
                        'no_telp'     => trim($row['notelp'] ?? ''),
                    ];
                    if (!empty($row['qrCode'])) {
                        $updateData['QR_id_qr'] = $row['qrCode'];
                    }
                    DB::table('warga')->where('no_kk', $nkk)->update($updateData);

                    // Pastikan baris distribusi ada walaupun warga sudah ada
                    $distExists = DB::table('distribusi')
                        ->where('warga_no_kk', $nkk)
                        ->exists();

                    if (!$distExists) {
                        DB::table('distribusi')->insert([
                            'warga_no_kk'    => $nkk,
                            'st_pengambilan' => 'pending',
                            'mtd_pengambilan' => null,
                            'login'          => null,
                            'dowload_qr'     => null,
                        ]);
                    }

                    $updated++;
                } else {

                    // Create new record dengan raw SQL untuk avoid Eloquent timestamp issue
                    $insertData = [
                        'no_kk'       => $nkk,
                        'nama_kk'     => $nama,
                        'alamat'      => trim($row['alamat'] ?? ''),
                        'no_telp'     => trim($row['notelp'] ?? ''),
                        'id_penerima' => $nextId++,
                    
                    ];
                    if (!empty($row['qrCode'])) {
                        $insertData['QR_id_qr'] = $row['qrCode'];
                    }
                    DB::table('warga')->insert($insertData);
                   // Otomatis buat row distribusi untuk warga baru
DB::table('distribusi')->insert([
    'warga_no_kk'    => $nkk,
    'st_pengambilan' => 'pending',
    'mtd_pengambilan' => null,
    'login'          => null,
    'dowload_qr'     => null,
]);

\Log::info('Insert distribusi untuk: ' . $nkk);

$created++;
                }
                
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($idx + 1) . ": " . $e->getMessage();
                $failed++;
            }
        }
        
        DB::commit();

        // ✅ Return detailed response
        return response()->json([
            'success' => true,
            'message' => "✅ {$created} penerima baru, {$updated} diperbarui" . ($failed > 0 ? ", {$failed} gagal" : ''),
            'data' => [
                'created' => $created,
                'updated' => $updated,
                'failed' => $failed,
                'total' => $created + $updated,
                'errors' => $errors,
            ]
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => '❌ Gagal menyimpan penerima: ' . $e->getMessage(),
            'data' => null,
        ], 422);
    }
});

// Route: Update status login warga
Route::post('/warga/login', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'nkk'  => 'required|string',
        'nama' => 'required|string',
    ]);

    $nkk  = preg_replace('/\D+/', '', $data['nkk']);
    $nama = strtolower(trim(preg_replace('/\s+/', ' ', $data['nama'])));

    // Cek warga ada di DB
    $warga = DB::table('warga')
        ->where('no_kk', $nkk)
        ->whereRaw('LOWER(TRIM(nama_kk)) = ?', [$nama])
        ->first();

    if (!$warga) {
        return response()->json(['success' => false, 'message' => 'Warga tidak ditemukan'], 404);
    }

// Update login/status di distribusi
    DB::table('distribusi')
        ->where('warga_no_kk', $nkk)
       ->update([
           'login' => 'sudah_login',
           // admin UI membaca dowload_qr untuk badge "Sudah Login"
           'dowload_qr' => 'sudah_login',
       ]);


    return response()->json(['success' => true]);
});

// Route: Cek status pengambilan warga (polling dari halaman warga)
Route::get('/warga/status', function (\Illuminate\Http\Request $request) {
    $nkk = preg_replace('/\D+/', '', $request->query('nkk', ''));
    if (!$nkk) {
        return response()->json(['success' => false], 400);
    }

    $dist = DB::table('distribusi')
        ->where('warga_no_kk', $nkk)
        ->first();

    if (!$dist) {
        return response()->json(['success' => false, 'st_pengambilan' => 'pending']);
    }

    return response()->json([
        'success'         => true,
        'st_pengambilan'  => $dist->st_pengambilan ?? 'pending',
        'mtd_pengambilan' => $dist->mtd_pengambilan ?? null,
        'dowload_qr'      => $dist->dowload_qr ?? 'Belum',
        'updated_at'      => $dist->updated_at ?? null,
    ]);
});

Route::get('/api/tracking', function () {
    return response()->json([
        'success' => true,
        'steps' => []
    ]);
});

Route::post('/admin/api/distribusi/{idStok}/batalkan', function(\Illuminate\Http\Request $request, $idStok) {
    DB::table('distribusi')
        ->where('id_stok', $idStok)
        ->update([
            'st_pengambilan'  => 'pending',
            'mtd_pengambilan' => null,
        ]);

    return response()->json(['success' => true]);
});