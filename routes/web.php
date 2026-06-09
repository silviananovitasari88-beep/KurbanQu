<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WargaQrController;
use App\Models\Warga;
use App\Models\WargaUpload;

// Route untuk Halaman Depan / Dashboard Warga (yang kemarin)
Route::get('/', function () {
    return view('kurban.home');
});

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
        
        // ✅ Log ke warga_uploads (insert direct ke database)
        $now = date('Y-m-d H:i:s');
        $uploadId = DB::table('warga_uploads')->insertGetId([
            'filename' => 'web_upload_' . date('Y-m-d_H-i-s'),
            'jumlah_baris' => count($penerima),
            'mode' => $mode,
            'admin_id' => auth()->check() ? auth()->id() : null,
            'status' => 'pending',
            'uploaded_at' => $now,
        ]);
        
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
        
        // ✅ Update upload status
        DB::table('warga_uploads')->where('id', $uploadId)->update([
            'status' => $failed === 0 ? 'success' : 'failed',
            'error_message' => count($errors) > 0 ? implode("\n", $errors) : null,
            'processed_at' => $now,
        ]);
        
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
        
        // ✅ Log error
        if (isset($uploadId)) {
            DB::table('warga_uploads')->where('id', $uploadId)->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'processed_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => '❌ Gagal menyimpan penerima: ' . $e->getMessage(),
            'data' => null,
        ], 422);
    }
});