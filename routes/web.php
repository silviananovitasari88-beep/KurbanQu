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

// login warga
Route::post('/warga/login', [WargaQrController::class, 'login'])->name('warga.login');
Route::get('/warga/login', function() { return redirect('/'); }); // ← tambah ini


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


// ── API Tracking Proses Kurban (Admin → Warga realtime) ─────────────────────

// GET: ambil status tracking (dipanggil kurban.js setiap 10 detik)
Route::get('/api/tracking', function () {
    try {
        $raw = \Illuminate\Support\Facades\Cache::get('kurban_tracking_steps');
        $steps = $raw ? json_decode($raw, true) : [];

        // Default steps jika belum ada
        if (empty($steps)) {
            $steps = [
                ['label' => 'Penyembelihan', 'status' => 'pending', 'time' => '—'],
                ['label' => 'Pengulitan',    'status' => 'pending', 'time' => '—'],
                ['label' => 'Pencacahan',    'status' => 'pending', 'time' => '—'],
                ['label' => 'Penimbangan',   'status' => 'pending', 'time' => '—'],
                ['label' => 'Siap Diambil',  'status' => 'pending', 'time' => '—'],
            ];
        }

        return response()->json(['success' => true, 'steps' => $steps]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'steps' => []], 500);
    }
});

// POST: simpan status tracking dari admin
Route::post('/admin/api/tracking', function (\Illuminate\Http\Request $request) {
    $data = $request->validate(['steps' => 'required|array']);
    \Illuminate\Support\Facades\Cache::put('kurban_tracking_steps', json_encode($data['steps']), 86400);
    return response()->json(['success' => true]);
});