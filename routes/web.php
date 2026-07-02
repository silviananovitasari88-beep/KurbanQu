<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WargaQrController;
use App\Models\Warga;
use App\Http\Controllers\AuthController;

// ---- Auth ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/auth/login',    [AuthController::class, 'login'])->name('auth.do-login');
    Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');

// ---- Admin (protected) ----
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::get('/', fn() => view('admin.dashboard'));
    // Tambahkan route admin lainnya di sini
});

// Route untuk Halaman Depan / Dashboard Warga 
Route::get('/', function () {
    return view('kurban.home');
});

// qr download
Route::post('/warga/download-qr', [WargaQrController::class, 'download'])
    ->name('warga.download-qr');

                                      
//  return view('admin.dashboard');

Route::get('/admin/api/distribusi/snapshot', [AdminController::class, 'distribusiSnapshot']);
Route::post('/admin/api/distribusi/{idStok}/manual', [AdminController::class, 'updateDistribusiManual']);
Route::delete('/admin/api/import-temp', [AdminController::class, 'deleteTempImport']);
Route::delete('/admin/api/penerima', [AdminController::class, 'clearPenerimaData']);
Route::delete('/admin/api/penerima/clear-all', [AdminController::class, 'clearPenerimaData']);
Route::post('/warga/qr/download', [WargaQrController::class, 'download']);
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
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
                            'warga_no_kk'     => $nkk,
                            'st_pengambilan'  => 'pending',
                            'mtd_pengambilan' => null,
                            'login'           => null,
                            'dowload_qr'      => null,
                            'QR_id_qr'        => 0,   // placeholder; hapus setelah migration nullable
                        ]);
                    }

                    $updated++;
                } else {
                    // Hitung id_penerima & kode QR (P00001 dst)
                    $idPenerima = $nextId++;
                    $qrCode     = 'P' . str_pad((string) $idPenerima, 5, '0', STR_PAD_LEFT);

                    // Insert ke tabel warga dengan kode QR
                    DB::table('warga')->insert([
                        'no_kk'       => $nkk,
                        'nama_kk'     => $nama,
                        'alamat'      => trim($row['alamat'] ?? ''),
                        'no_telp'     => trim($row['notelp'] ?? ''),
                        'id_penerima' => $idPenerima,
                        'QR_id_qr'    => $qrCode,
                    ]);

                    // ── Insert ke distribusi ──────────────────────────────────
                    // QR_id_qr di distribusi adalah int FK ke tabel QR.id_qr
                    // Kita pakai 0 sebagai placeholder jika kolom masih NOT NULL.
                    // Setelah migration fix_distribusi_qr_nullable dijalankan,
                    // kolom ini jadi nullable dan tidak perlu value 0 lagi.
                    $distribusiRow = [
                        'warga_no_kk'     => $nkk,
                        'st_pengambilan'  => 'pending',
                        'mtd_pengambilan' => null,
                        'login'           => null,
                        'dowload_qr'      => null,
                        'QR_id_qr'        => 0,   // placeholder; hapus setelah migration nullable
                    ];
                    DB::table('distribusi')->insert($distribusiRow);

                    \Log::info('[KurbanQu] Insert distribusi OK untuk NKK: ' . $nkk);
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

    $now = now();
    $updated = DB::table('distribusi')
        ->where('warga_no_kk', $nkk)
        ->update([
            'login' => 'sudah_login',
            // admin UI membaca dowload_qr untuk badge "Sudah Login"
            'dowload_qr' => 'sudah_login',
            'login_at' => $now,
        ]);

    if (!$updated) {
        return response()->json(['success' => false, 'message' => 'Data distribusi tidak ditemukan'], 404);
    }

    $queue = DB::table('distribusi')
        ->whereNotNull('login_at')
        ->where('login_at', '<', $now)
        ->count() + 1;

    return response()->json([
        'success' => true,
        'data' => [
            'nkk' => $warga->no_kk,
            'nama' => $warga->nama_kk,
            'alamat' => $warga->alamat ?? '',
            'notelp' => $warga->no_telp ?? '',
            'id_penerima' => $warga->id_penerima ?? null,
            'qrCode' => $warga->QR_id_qr ?? null,
            'queue' => $queue,
        ],
    ]);
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

// ── GET: User polling status tracking ──────────────────────────────────────
Route::get('/api/tracking', function () {
    $steps = DB::table('tracking_steps')->orderBy('urutan')->get();

    if ($steps->isEmpty()) {
        // Default 5 tahap jika belum ada data
        $default = [
            ['urutan'=>1,'label'=>'Penyembelihan','status'=>'pending','time'=>null],
            ['urutan'=>2,'label'=>'Pengulitan',   'status'=>'pending','time'=>null],
            ['urutan'=>3,'label'=>'Pencacahan',   'status'=>'pending','time'=>null],
            ['urutan'=>4,'label'=>'Penimbangan',  'status'=>'pending','time'=>null],
            ['urutan'=>5,'label'=>'Siap Diambil', 'status'=>'pending','time'=>null],
        ];
        return response()->json([
            'success' => true,
            'steps' => array_map(fn($s) => [
                'status' => $s['status'],
                'time'   => $s['time'] ?? '—',
            ], $default),
        ]);
    }

    return response()->json([
        'success' => true,
        'steps' => $steps->map(fn($s) => [
            'status' => $s->status,
            'time'   => $s->time ?? '—',
        ])->values(),
    ]);
});

// ── POST: Admin update status tracking ─────────────────────────────────────
Route::post('/admin/api/tracking/{urutan}', function (\Illuminate\Http\Request $request, $urutan) {
    $data = $request->validate([
        'status' => 'required|in:pending,active,done',
    ]);

    $exists = DB::table('tracking_steps')->where('urutan', $urutan)->exists();
    $labels = ['Penyembelihan','Pengulitan','Pencacahan','Penimbangan','Siap Diambil'];
    $now = now()->format('H:i') . ' WIB';

    if ($exists) {
        DB::table('tracking_steps')->where('urutan', $urutan)->update([
            'status' => $data['status'],
            'time'   => $data['status'] !== 'pending' ? $now : null,
        ]);
    } else {
        DB::table('tracking_steps')->insert([
            'urutan' => $urutan,
            'label'  => $labels[$urutan - 1] ?? "Tahap $urutan",
            'status' => $data['status'],
            'time'   => $data['status'] !== 'pending' ? $now : null,
        ]);
    }

    return response()->json(['success' => true]);
});

// ── POST: Admin reset semua tracking ───────────────────────────────────────
Route::post('/admin/api/tracking/reset', function () {
    DB::table('tracking_steps')->update(['status' => 'pending', 'time' => null]);
    return response()->json(['success' => true]);
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

// ── Hewan ────────────────────────────────────────────────────────────────
Route::get('/admin/api/hewan', [AdminController::class, 'getHewan']);
Route::post('/admin/api/hewan', [AdminController::class, 'storeHewan']);
Route::delete('/admin/api/hewan/{idHewan}', [AdminController::class, 'deleteHewan']);

// ── Mudhohi ──────────────────────────────────────────────────────────────
Route::get('/admin/api/mudhohi', [AdminController::class, 'getMudhohi']);
Route::post('/admin/api/mudhohi', [AdminController::class, 'storeMudhohi']);
Route::delete('/admin/api/mudhohi/{idMudhohi}', [AdminController::class, 'deleteMudhohi']);

Route::get('/api/hewan', [AdminController::class, 'getHewan']);
Route::get('/api/mudhohi', [AdminController::class, 'getMudhohi']);