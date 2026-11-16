<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Storage;
use App\Models\Warga;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // Dashboard admin
public function dashboard()
{
    return view('admin.dashboard');
}

// Batalkan pengambilan (set status kembali ke pending dan kembalikan stok +1)
public function batalkanDistribusi(Request $request, $idStok)
{
    $row = DB::table('distribusi')->where('id_stok', $idStok)->first();
    if ($row && in_array(strtolower($row->st_pengambilan ?? ''), ['selesai', 'diambil'])) {
        $wargaNama = DB::table('warga')->where('no_kk', $row->warga_no_kk)->value('nama_kk') ?? $row->warga_no_kk;
        $stokData = $this->tambahStokInternal(1, "Pembatalan distribusi #{$idStok} - {$wargaNama} (KK: {$row->warga_no_kk})", 'batal', $row->warga_no_kk);
    } else {
        $stokData = $this->getStokDataInternal();
    }

    DB::table('distribusi')
        ->where('id_stok', $idStok)
        ->update([
            'st_pengambilan'  => 'pending',
            'mtd_pengambilan' => null,
        ]);

    return response()->json([
        'success' => true,
        'stok'    => $stokData,
    ]);
}

	public function distribusiSnapshot(): JsonResponse
	{
		$rows = DB::table('distribusi as d')
			->leftJoin('warga as w', 'w.no_kk', '=', 'd.warga_no_kk')
            ->leftJoin('qr as q', 'q.id_qr', '=', 'd.QR_id_qr')
			->select([
				'd.id_stok',
				'd.warga_no_kk',
				'd.QR_id_qr',
				'd.dowload_qr',
				'd.st_pengambilan',
				'd.mtd_pengambilan',
				'd.login',
                'd.status_login',
				'w.nama_kk',
				'w.id_penerima',
				'q.jam_pengambilan',
			])
			->orderBy('d.id_stok')
			->get();

		$mapped = [];
		foreach ($rows as $row) {
			$key = (string) ($row->warga_no_kk ?? '');
			if ($key === '') {
				continue;
			}

			$mapped[$key] = [
				'id_stok' => $row->id_stok,
				'warga_no_kk' => $key,
				'qr_id_qr' => $row->QR_id_qr,
				'nama_kk' => $row->nama_kk,
				'id_penerima' => $row->id_penerima,
				'dowload_qr' => $row->dowload_qr,
				'st_pengambilan' => $row->st_pengambilan,
				'mtd_pengambilan' => $row->mtd_pengambilan,
				'login' => $row->login,
				'status_login' => $row->status_login,
				'updated_at' => null,
				'jam_pengambilan' => $row->jam_pengambilan,
			];
		}

		return response()->json([
			'success' => true,
			'data' => $mapped,
		]);
	}

	public function updateDistribusiManual(Request $request, int $idStok): JsonResponse
	{
		$data = $request->validate([
			'warga_no_kk' => ['required', 'string'],
			'qr_id_qr' => ['nullable', 'string'],
		]);

		$now = now();

		$query = DB::table('distribusi')->where('id_stok', $idStok);
		$row = $query->first();

		if (!$row) {
			return response()->json([
				'success' => false,
				'message' => 'Baris distribusi tidak ditemukan.',
			], 404);
		}

		if ((string) $row->warga_no_kk !== (string) $data['warga_no_kk']) {
			return response()->json([
				'success' => false,
				'message' => 'Data warga tidak cocok dengan baris distribusi.',
			], 422);
		}

		$metode = $request->input('metode') === 'QR' ? 'QR' : 'Manual';

		// ── Validasi: QR scan hanya boleh jika tahap "Siap Diambil" sudah selesai ──
		if ($metode === 'QR') {
			$siapDiambilStep = DB::table('tracking_steps')
				->where('urutan', 5)
				->where('status', 'done')
				->exists();

			if (!$siapDiambilStep) {
				return response()->json([
					'success' => false,
					'message' => '⏳ QR belum dapat dipindai. Proses penyembelihan kurban belum mencapai tahap "Siap Diambil". Mohon tunggu konfirmasi dari panitia.',
					'error_code' => 'QR_NOT_READY',
				], 422);
			}
		}

		// ── Validasi & Pemotongan Stok Otomatis ──
		$isAlreadyClaimed = in_array(strtolower($row->st_pengambilan ?? ''), ['selesai', 'diambil']);
		if (!$isAlreadyClaimed) {
			$stokData = $this->getStokDataInternal();
			if ($stokData['stok_tersedia'] <= 0) {
				return response()->json([
					'success' => false,
					'error_code' => 'STOK_HABIS',
					'message' => '⚠️ Stok daging kurban saat ini kosong (0 paket)! Silakan tambah stok terlebih dahulu sebelum verifikasi pengambilan.',
					'stok' => $stokData,
				], 422);
			}

			$wargaNama = DB::table('warga')->where('no_kk', $row->warga_no_kk)->value('nama_kk') ?? $row->warga_no_kk;
			$stokData = $this->kurangiStokInternal(1, "Pengambilan ({$metode}) - {$wargaNama} (KK: {$row->warga_no_kk})", $row->warga_no_kk);
		} else {
			$stokData = $this->getStokDataInternal();
		}

		$updateData = [
    		'st_pengambilan'  => 'selesai',
    		'mtd_pengambilan' => $metode,
		];

		if (Schema::hasColumn('distribusi', 'updated_at')) {
			$updateData['updated_at'] = $now;
		}

		$query->update($updateData);

		if (Schema::hasTable('qr') && Schema::hasColumn('qr', 'jam_pengambilan')) {
            $qrId = $data['qr_id_qr'] ?? $row->QR_id_qr ?? null;
            if ($qrId !== null) {
                $qrIdNorm = preg_replace('/\D/', '', (string) $qrId);
                if ($qrIdNorm === '') {
                    $qrIdNorm = $qrId;
                }
                DB::table('qr')->where('id_qr', $qrIdNorm)->update([
                    'jam_pengambilan' => $now->toDateTimeString(),
                ]);
            }
		}

		return response()->json([
		    'success' => true,
		    'message' => 'Status distribusi diperbarui.',
		    'stok' => $stokData,
		    'data' => [
		        'id_stok' => $idStok,
		        'warga_no_kk' => $data['warga_no_kk'],
		        'st_pengambilan' => 'selesai',
		        'mtd_pengambilan' => $metode,
		        'updated_at' => $now->toDateTimeString(),
		    ],
		]);
	}

    public function clearAllPenerima(): JsonResponse
	{
	    try {
	        DB::table('distribusi')->delete();
	        DB::table('qr')->delete();
	        DB::table('warga')->delete();
	        DB::statement('ALTER TABLE warga AUTO_INCREMENT = 1');
	        DB::statement('ALTER TABLE distribusi AUTO_INCREMENT = 1');
	        DB::statement('ALTER TABLE qr AUTO_INCREMENT = 1');

	        return response()->json([
	            'success' => true,
	            'message' => 'Semua data penerima dan nomor antrian berhasil dihapus.',
	        ]);
	    } catch (\Exception $e) {
	        return response()->json([
	            'success' => false,
	            'message' => 'Gagal menghapus data: ' . $e->getMessage(),
	        ], 500);
	    }
	}

	public function deleteTempImport(Request $request): JsonResponse
	{
		$data = $request->validate([
			'temp_file' => ['required', 'string', 'max:255'],
		]);

		$tempFile = basename(str_replace(['\\', '..'], ['/', ''], $data['temp_file']));
		$relativePath = 'imports/tmp/' . $tempFile;

		if (!Storage::disk('local')->exists($relativePath)) {
			return response()->json([
				'success' => true,
				'message' => 'File sementara tidak ditemukan.',
			]);
		}

		Storage::disk('local')->delete($relativePath);

		return response()->json([
			'success' => true,
			'message' => 'File sementara berhasil dihapus.',
		]);
	}

	public function clearPenerimaData(): JsonResponse
	{
		$deleted = [];

		DB::transaction(function () use (&$deleted) {
            foreach (['distribusi', 'warga', 'qr'] as $table) {
				if (!Schema::hasTable($table)) {
					continue;
				}

				DB::table($table)->delete();
				$deleted[] = $table;
			}
		});

		return response()->json([
			'success' => true,
			'message' => 'Semua data penerima berhasil dihapus.',
			'data' => [
				'deleted_tables' => $deleted,
			],
		]);
	}

	public function importWarga(Request $request): JsonResponse
	{
	    $request->validate([
	        'file' => ['required', 'file', 'mimes:csv,txt'],
	    ]);

	    $file = $request->file('file');
	    $rows = array_map('str_getcsv', file($file->getRealPath()));
	    $header = array_shift($rows);

	    DB::transaction(function () use ($rows) {
	        foreach ($rows as $row) {
	            if (count($row) < 3) continue;

	            $noKk    = trim($row[0]);
	            $namaKk  = trim($row[1]);
	            $alamat  = trim($row[2] ?? '');

	            $idPenerima = DB::table('warga')->insertGetId([
	                'no_kk'   => $noKk,
	                'nama_kk' => $namaKk,
	                'alamat'  => $alamat,
	            ]);

				$idQr = DB::table('qr')->insertGetId([
	                'no_antrian'     => null,
	                'loc_pengambilan' => 'Masjid Al-Ikhlas',
	                'dur_sesi'       => 15,
	            ]);

	            DB::table('warga')->where('no_kk', $noKk)->update([
	                'QR_id_qr'    => $idQr,
	                'id_penerima' => $idPenerima,
	            ]);

	            DB::table('distribusi')->insert([
	                'warga_no_kk'    => $noKk,
	                'QR_id_qr'       => $idQr,
	                'st_pengambilan' => 'pending',
	                'mtd_pengambilan' => null,
	                'login'          => 'belum_login',
	                'dowload_qr'     => 'belum',
	                'status_login'   => 'Belum Login',
	            ]);
	        }
	    });

	    return response()->json(['success' => true, 'message' => 'Import berhasil.']);
	}

	// ═══════════════════════════════════════════
    // HEWAN
    // ═══════════════════════════════════════════
    public function getHewan(): JsonResponse
    {
        $hewan = DB::table('hewan')->orderBy('id_hewan')->get();
        return response()->json(['success' => true, 'data' => $hewan]);
    }

    public function storeHewan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'jenis'      => ['required', 'in:Sapi,Domba,Kambing'],
            'sehat'      => ['required', 'in:Sehat,Tidak Sehat'],
            'cacat'      => ['required', 'in:Cacat,Tidak Cacat'],
            'umur'       => ['nullable', 'string', 'max:50'],
            'berat'      => ['nullable', 'string', 'max:50'],
            'st_syariat' => ['required', 'boolean'],
        ]);

        $id = DB::table('hewan')->insertGetId([
            'jenis'                => $data['jenis'],
            'sehat'                => $data['sehat'],
            'cacat'                => $data['cacat'],
            'umur'                 => $data['umur'] ?? null,
            'berat'                => $data['berat'] ?? null,
            'st_syariat'           => $data['st_syariat'],
            'admin_id_admin'       => auth()->id() ?? 1,
            
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hewan berhasil ditambahkan',
            'data' => ['id_hewan' => $id],
        ]);
    }

    public function deleteHewan(int $idHewan): JsonResponse
    {
        DB::table('mudhohi')->where('hewan_id_hewan', $idHewan)->delete();
        DB::table('hewan')->where('id_hewan', $idHewan)->delete();

        return response()->json(['success' => true, 'message' => 'Hewan berhasil dihapus']);
    }

    // ════════════════════════════════════════
    // MUDHOHI
    // ════════════════════════════════════════

	public function getMudhohi(): JsonResponse
    {
        $mudhohi = DB::table('mudhohi')->orderBy('id_mudhohi')->get();
        return response()->json(['success' => true, 'data' => $mudhohi]);
    }

    public function storeMudhohi(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_mudhohi'   => ['required', 'string', 'max:45'],
            'nama_ayah'      => ['nullable', 'string', 'max:45'],
            'alamat'         => ['nullable', 'string', 'max:100'],
            'notelp_mudhohi' => ['nullable', 'numeric'],
            'req_bagian'     => ['nullable', 'string', 'max:45'],
            'hewan_id_hewan' => ['required', 'integer', 'exists:hewan,id_hewan'],
        ]);

        $id = DB::table('mudhohi')->insertGetId([
            'nama_mudhohi'   => $data['nama_mudhohi'],
            'nama_ayah'      => $data['nama_ayah'] ?? null,
            'alamat'         => $data['alamat'] ?? null,
            'notelp_mudhohi' => $data['notelp_mudhohi'] ?? null,
            'req_bagian'     => $data['req_bagian'] ?? null,
            'admin_id_admin' => auth()->id() ?? 1,
            'hewan_id_hewan' => $data['hewan_id_hewan'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mudhohi berhasil ditambahkan',
            'data' => ['id_mudhohi' => $id],
        ]);
    }

    public function deleteMudhohi(int $idMudhohi): JsonResponse
    {
        DB::table('mudhohi')->where('id_mudhohi', $idMudhohi)->delete();
        return response()->json(['success' => true, 'message' => 'Mudhohi berhasil dihapus']);
    }

    // ════════════════════════════════════════
    // ⭐ DELETE WARGA + DISTRIBUSI (HAPUS KEDUANYA)
    // ════════════════════════════════════════
    public function deleteWarga($noKk): JsonResponse
    {
        try {
            Log::info("=== DELETE WARGA + DISTRIBUSI ===");
            Log::info("No KK: " . $noKk);

            // Cek apakah warga ada
            $warga = Warga::where('no_kk', $noKk)->first();
            
            if (!$warga) {
                Log::warning("Warga tidak ditemukan: " . $noKk);
                return response()->json([
                    'success' => false,
                    'message' => 'Data warga dengan No KK ' . $noKk . ' tidak ditemukan'
                ], 404);
            }

            $nama = $warga->nama_kk;

            DB::beginTransaction();

            try {
                // ⭐ 1. HAPUS DATA DISTRIBUSI TERKAIT
                $deletedDistribusi = DB::table('distribusi')
                    ->where('warga_no_kk', $noKk)
                    ->delete();
                
                Log::info("Distribusi dihapus: " . $deletedDistribusi . " baris");

                // ⭐ 2. HAPUS DATA WARGA
                $deletedWarga = Warga::where('no_kk', $noKk)->delete();
                
                Log::info("Warga dihapus: " . $deletedWarga . " baris");

                DB::commit();

                Log::info("SUKSES hapus: {$nama} ({$noKk})");

                return response()->json([
                    'success' => true,
                    'message' => "✅ Data warga '{$nama}' dan distribusi terkait berhasil dihapus",
                    'data' => [
                        'no_kk' => $noKk,
                        'nama' => $nama,
                        'deleted_distribusi' => $deletedDistribusi,
                        'deleted_warga' => $deletedWarga
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error dalam transaction: " . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("Gagal hapus warga: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    // ════════════════════════════════════════
    // ⭐ GET LIST PENERIMA
    // ════════════════════════════════════════
    public function getPenerimaList(): JsonResponse
    {
        try {
            $warga = DB::table('warga')
                ->select('no_kk', 'nama_kk', 'alamat', 'no_telp', 'id_penerima', 'QR_id_qr')
                ->orderBy('id_penerima', 'asc')
                ->get();
            
            $result = $warga->map(function($item) {
                $dist = DB::table('distribusi')
                    ->where('warga_no_kk', $item->no_kk)
                    ->first();
                    
                $status = 'BELUM AMBIL';
                if ($dist && ($dist->st_pengambilan === 'diambil' || $dist->st_pengambilan === 'selesai')) {
                    $status = 'SUDAH AMBIL';
                }
                    
                return [
                    'no_kk' => $item->no_kk,
                    'nama_kk' => $item->nama_kk ?? '-',
                    'alamat' => $item->alamat ?? '-',
                    'no_telp' => $item->no_telp ?? '-',
                    'id_penerima' => $item->id_penerima,
                    'qr_code' => $item->QR_id_qr ?? 'P' . str_pad($item->id_penerima ?? 0, 5, '0', STR_PAD_LEFT),
                    'status' => $status,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'total' => $result->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error getPenerimaList: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ════════════════════════════════════════
    // ⭐ LOOKUP WARGA BERDASARKAN QR CODE (untuk scan kamera)
    // ════════════════════════════════════════
    public function scanByQrCode(string $qrCode): JsonResponse
    {
        // Normalisasi: trim whitespace & uppercase
        $qrCode = trim($qrCode);
        $qrNorm = strtoupper($qrCode);

        // Strategi 1: QR code format 'PXXXXX' — cari id_penerima dari kode
        $idPenerimaFromCode = null;
        if (preg_match('/^P0*(\d+)$/i', $qrNorm, $matches)) {
            $idPenerimaFromCode = (int) $matches[1];
        }

        // Strategi 2: cari berdasarkan QR_id_qr di tabel warga
        $wargaQuery = DB::table('warga as w')
            ->leftJoin('distribusi as d', 'w.no_kk', '=', 'd.warga_no_kk')
            ->leftJoin('qr as q', 'q.id_qr', '=', 'w.QR_id_qr');

        // Cari dengan beberapa kriteria
        $warga = null;

        // Coba cocokkan dengan id_penerima dari kode P-format
        if ($idPenerimaFromCode !== null) {
            $warga = (clone $wargaQuery)
                ->where('w.id_penerima', $idPenerimaFromCode)
                ->select('w.no_kk', 'w.nama_kk', 'w.alamat', 'w.no_telp', 'w.id_penerima', 'w.QR_id_qr',
                         'd.id_stok', 'd.st_pengambilan', 'd.mtd_pengambilan', 'q.no_antrian')
                ->first();
        }

        // Jika belum ditemukan, coba cocokkan QR_id_qr langsung (numerik)
        if (!$warga) {
            $qrNumeric = preg_replace('/\D/', '', $qrCode);
            if ($qrNumeric !== '') {
                $warga = (clone $wargaQuery)
                    ->where('w.QR_id_qr', $qrNumeric)
                    ->select('w.no_kk', 'w.nama_kk', 'w.alamat', 'w.no_telp', 'w.id_penerima', 'w.QR_id_qr',
                             'd.id_stok', 'd.st_pengambilan', 'd.mtd_pengambilan', 'q.no_antrian')
                    ->first();
            }
        }

        // Jika masih belum, coba cocokkan dengan no_antrian di tabel qr
        if (!$warga && $idPenerimaFromCode !== null) {
            $warga = (clone $wargaQuery)
                ->where('q.no_antrian', $idPenerimaFromCode)
                ->select('w.no_kk', 'w.nama_kk', 'w.alamat', 'w.no_telp', 'w.id_penerima', 'w.QR_id_qr',
                         'd.id_stok', 'd.st_pengambilan', 'd.mtd_pengambilan', 'q.no_antrian')
                ->first();
        }

        if (!$warga) {
            return response()->json([
                'success'   => false,
                'message'   => "QR tidak dikenali: {$qrCode}",
                'error_code' => 'QR_NOT_FOUND',
                'scanned'   => $qrCode,
            ], 404);
        }

        $claimed = in_array(strtolower($warga->st_pengambilan ?? ''), ['selesai', 'diambil']);
        $qrCodeDisplay = $warga->QR_id_qr
            ? ('P' . str_pad($warga->id_penerima, 5, '0', STR_PAD_LEFT))
            : $qrCode;

        return response()->json([
            'success'         => true,
            'claimed'         => $claimed,
            'data'            => [
                'no_kk'          => $warga->no_kk,
                'nama_kk'        => $warga->nama_kk,
                'alamat'         => $warga->alamat ?? '-',
                'no_telp'        => $warga->no_telp ?? '-',
                'id_penerima'    => $warga->id_penerima,
                'id_stok'        => $warga->id_stok,
                'qr_code'        => $qrCodeDisplay,
                'no_antrian'     => $warga->no_antrian ?? $warga->id_penerima,
                'st_pengambilan' => $warga->st_pengambilan ?? 'pending',
            ],
        ]);
    }

    // ════════════════════════════════════════
    // ⭐ VALIDASI QR SCAN — cek apakah siap diambil
    // ════════════════════════════════════════
    public function getQrScanValidation(): \Illuminate\Http\JsonResponse
    {
        $step5 = DB::table('tracking_steps')
            ->where('urutan', 5)
            ->first();

        $allSteps = DB::table('tracking_steps')
            ->orderBy('urutan')
            ->get();

        $siapDiambil = $step5 && $step5->status === 'done';
        $anyActive   = $allSteps->contains(fn($s) => in_array($s->status, ['active', 'done']));

        return response()->json([
            'success'       => true,
            'qr_ready'      => $siapDiambil,
            'any_progress'  => $anyActive,
            'step5_status'  => $step5->status ?? 'pending',
            'step5_time'    => $step5->time   ?? null,
            'steps'         => $allSteps->map(fn($s) => [
                'urutan' => $s->urutan,
                'label'  => $s->label  ?? null,
                'status' => $s->status,
                'time'   => $s->time   ?? null,
            ]),
        ]);
    }

    // ════════════════════════════════════════
    // ⭐ SETTINGS — Pengaturan Sistem (Tanggal Kurban, Lokasi, dll)
    // ════════════════════════════════════════
    public function getSettings(): \Illuminate\Http\JsonResponse
    {
        $defaultSettings = [
            'tanggal_kurban'     => null, // format: YYYY-MM-DD
            'lokasi_pengambilan' => 'Masjid Al-Ikhlas', // ✅ TAMBAHAN BARU
        ];

        if (Storage::disk('local')->exists('settings.json')) {
            $settings = json_decode(Storage::disk('local')->get('settings.json'), true);
            $defaultSettings = array_merge($defaultSettings, $settings ?: []);
        }

        return response()->json([
            'success' => true,
            'data'    => $defaultSettings,
        ]);
    }

    public function saveSettings(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'tanggal_kurban'     => 'nullable|date_format:Y-m-d',
            'lokasi_pengambilan' => 'nullable|string|max:255', // ✅ TAMBAHAN BARU
        ]);

        $settings = [];
        if (Storage::disk('local')->exists('settings.json')) {
            $settings = json_decode(Storage::disk('local')->get('settings.json'), true) ?: [];
        }

        $settings['tanggal_kurban'] = $data['tanggal_kurban'];

        // ✅ TAMBAHAN BARU — kalau lokasi dikirim & tidak kosong, simpan.
        //    Kalau kosong/tidak dikirim, biarkan nilai lama (jangan ditimpa null)
        if (!empty($data['lokasi_pengambilan'])) {
            $settings['lokasi_pengambilan'] = $data['lokasi_pengambilan'];
        }

        Storage::disk('local')->put('settings.json', json_encode($settings, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan.',
            'data'    => $settings,
        ]);
    }

    // ════════════════════════════════════════
    // ⭐ STOK KURBAN — Manajemen & Pemotongan Otomatis
    // ════════════════════════════════════════

    private function initStokTables(): void
    {
        try {
            if (!Schema::hasTable('stok_kurban')) {
                Schema::create('stok_kurban', function (Blueprint $table) {
                    $table->id();
                    $table->integer('stok_tersedia')->default(0);
                    $table->integer('total_masuk')->default(0);
                    $table->integer('total_keluar')->default(0);
                    $table->timestamps();
                });

                DB::table('stok_kurban')->insert([
                    'id'            => 1,
                    'stok_tersedia' => 0,
                    'total_masuk'   => 0,
                    'total_keluar'  => 0,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            if (!Schema::hasTable('stok_log')) {
                Schema::create('stok_log', function (Blueprint $table) {
                    $table->id();
                    $table->string('tipe', 30)->default('masuk'); // masuk, keluar, batal, penyesuaian
                    $table->integer('jumlah');
                    $table->integer('stok_sebelum');
                    $table->integer('stok_sesudah');
                    $table->string('keterangan')->nullable();
                    $table->string('referensi')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal inisialisasi tabel stok di database: ' . $e->getMessage());
        }
    }

    private function getStokDataInternal(): array
    {
        $this->initStokTables();

        try {
            if (Schema::hasTable('stok_kurban')) {
                $row = DB::table('stok_kurban')->where('id', 1)->first();
                if ($row) {
                    $logs = [];
                    if (Schema::hasTable('stok_log')) {
                        $logs = DB::table('stok_log')
                            ->orderBy('id', 'desc')
                            ->limit(50)
                            ->get()
                            ->map(fn($l) => [
                                'id'           => $l->id,
                                'tipe'         => $l->tipe,
                                'jumlah'       => (int) $l->jumlah,
                                'stok_sebelum' => (int) $l->stok_sebelum,
                                'stok_sesudah' => (int) $l->stok_sesudah,
                                'keterangan'   => $l->keterangan,
                                'referensi'    => $l->referensi,
                                'created_at'   => (string) $l->created_at,
                            ])
                            ->all();
                    }

                    $data = [
                        'stok_tersedia' => (int) $row->stok_tersedia,
                        'total_masuk'   => (int) $row->total_masuk,
                        'total_keluar'  => (int) $row->total_keluar,
                        'riwayat'       => $logs,
                    ];

                    Storage::disk('local')->put('stok.json', json_encode($data, JSON_PRETTY_PRINT));
                    return $data;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal query stok DB: ' . $e->getMessage());
        }

        // Fallback file JSON
        if (Storage::disk('local')->exists('stok.json')) {
            $json = json_decode(Storage::disk('local')->get('stok.json'), true);
            if (is_array($json)) {
                return array_merge([
                    'stok_tersedia' => 0,
                    'total_masuk'   => 0,
                    'total_keluar'  => 0,
                    'riwayat'       => [],
                ], $json);
            }
        }

        return [
            'stok_tersedia' => 0,
            'total_masuk'   => 0,
            'total_keluar'  => 0,
            'riwayat'       => [],
        ];
    }

    private function tambahStokInternal(int $jumlah, ?string $keterangan = null, string $tipe = 'masuk', ?string $referensi = null): array
    {
        $this->initStokTables();
        $now = now();

        try {
            if (Schema::hasTable('stok_kurban')) {
                $row = DB::table('stok_kurban')->where('id', 1)->first();
                $sebelum = $row ? (int) $row->stok_tersedia : 0;
                $masukLama = $row ? (int) $row->total_masuk : 0;
                $sesudah = $sebelum + $jumlah;

                $update = [
                    'stok_tersedia' => $sesudah,
                    'updated_at'    => $now,
                ];
                if ($tipe === 'masuk') {
                    $update['total_masuk'] = $masukLama + $jumlah;
                }

                if ($row) {
                    DB::table('stok_kurban')->where('id', 1)->update($update);
                } else {
                    $update['id'] = 1;
                    $update['created_at'] = $now;
                    $update['total_keluar'] = 0;
                    DB::table('stok_kurban')->insert($update);
                }

                if (Schema::hasTable('stok_log')) {
                    DB::table('stok_log')->insert([
                        'tipe'         => $tipe,
                        'jumlah'       => $jumlah,
                        'stok_sebelum' => $sebelum,
                        'stok_sesudah' => $sesudah,
                        'keterangan'   => $keterangan,
                        'referensi'    => $referensi,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }

                return $this->getStokDataInternal();
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal tambahStokInternal DB: ' . $e->getMessage());
        }

        // Fallback JSON file
        $data = $this->getStokDataInternal();
        $sebelum = (int) $data['stok_tersedia'];
        $sesudah = $sebelum + $jumlah;
        $data['stok_tersedia'] = $sesudah;
        if ($tipe === 'masuk') {
            $data['total_masuk'] = (int) $data['total_masuk'] + $jumlah;
        }
        array_unshift($data['riwayat'], [
            'id'           => time(),
            'tipe'         => $tipe,
            'jumlah'       => $jumlah,
            'stok_sebelum' => $sebelum,
            'stok_sesudah' => $sesudah,
            'keterangan'   => $keterangan,
            'referensi'    => $referensi,
            'created_at'   => $now->toDateTimeString(),
        ]);
        Storage::disk('local')->put('stok.json', json_encode($data, JSON_PRETTY_PRINT));
        return $data;
    }

    private function kurangiStokInternal(int $jumlah = 1, ?string $keterangan = null, ?string $referensi = null): array
    {
        $this->initStokTables();
        $now = now();

        try {
            if (Schema::hasTable('stok_kurban')) {
                $row = DB::table('stok_kurban')->where('id', 1)->first();
                $sebelum = $row ? (int) $row->stok_tersedia : 0;
                $keluarLama = $row ? (int) $row->total_keluar : 0;
                $sesudah = max(0, $sebelum - $jumlah);

                $update = [
                    'stok_tersedia' => $sesudah,
                    'total_keluar'  => $keluarLama + $jumlah,
                    'updated_at'    => $now,
                ];

                if ($row) {
                    DB::table('stok_kurban')->where('id', 1)->update($update);
                } else {
                    $update['id'] = 1;
                    $update['total_masuk'] = 0;
                    $update['created_at'] = $now;
                    DB::table('stok_kurban')->insert($update);
                }

                if (Schema::hasTable('stok_log')) {
                    DB::table('stok_log')->insert([
                        'tipe'         => 'keluar',
                        'jumlah'       => $jumlah,
                        'stok_sebelum' => $sebelum,
                        'stok_sesudah' => $sesudah,
                        'keterangan'   => $keterangan,
                        'referensi'    => $referensi,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }

                return $this->getStokDataInternal();
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal kurangiStokInternal DB: ' . $e->getMessage());
        }

        // Fallback JSON file
        $data = $this->getStokDataInternal();
        $sebelum = (int) $data['stok_tersedia'];
        $sesudah = max(0, $sebelum - $jumlah);
        $data['stok_tersedia'] = $sesudah;
        $data['total_keluar'] = (int) $data['total_keluar'] + $jumlah;
        array_unshift($data['riwayat'], [
            'id'           => time(),
            'tipe'         => 'keluar',
            'jumlah'       => $jumlah,
            'stok_sebelum' => $sebelum,
            'stok_sesudah' => $sesudah,
            'keterangan'   => $keterangan,
            'referensi'    => $referensi,
            'created_at'   => $now->toDateTimeString(),
        ]);
        Storage::disk('local')->put('stok.json', json_encode($data, JSON_PRETTY_PRINT));
        return $data;
    }

    private function aturStokInternal(int $stokTersedia, ?string $keterangan = null): array
    {
        $this->initStokTables();
        $now = now();
        $stokTersedia = max(0, $stokTersedia);

        try {
            if (Schema::hasTable('stok_kurban')) {
                $row = DB::table('stok_kurban')->where('id', 1)->first();
                $sebelum = $row ? (int) $row->stok_tersedia : 0;

                $update = [
                    'stok_tersedia' => $stokTersedia,
                    'updated_at'    => $now,
                ];

                if ($row) {
                    DB::table('stok_kurban')->where('id', 1)->update($update);
                } else {
                    $update['id'] = 1;
                    $update['total_masuk'] = $stokTersedia;
                    $update['total_keluar'] = 0;
                    $update['created_at'] = $now;
                    DB::table('stok_kurban')->insert($update);
                }

                if (Schema::hasTable('stok_log')) {
                    DB::table('stok_log')->insert([
                        'tipe'         => 'penyesuaian',
                        'jumlah'       => abs($stokTersedia - $sebelum),
                        'stok_sebelum' => $sebelum,
                        'stok_sesudah' => $stokTersedia,
                        'keterangan'   => $keterangan ?: 'Koreksi stok oleh admin',
                        'referensi'    => null,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }

                return $this->getStokDataInternal();
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal aturStokInternal DB: ' . $e->getMessage());
        }

        $data = $this->getStokDataInternal();
        $sebelum = (int) $data['stok_tersedia'];
        $data['stok_tersedia'] = $stokTersedia;
        array_unshift($data['riwayat'], [
            'id'           => time(),
            'tipe'         => 'penyesuaian',
            'jumlah'       => abs($stokTersedia - $sebelum),
            'stok_sebelum' => $sebelum,
            'stok_sesudah' => $stokTersedia,
            'keterangan'   => $keterangan ?: 'Koreksi stok oleh admin',
            'referensi'    => null,
            'created_at'   => $now->toDateTimeString(),
        ]);
        Storage::disk('local')->put('stok.json', json_encode($data, JSON_PRETTY_PRINT));
        return $data;
    }

    public function getStok(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->getStokDataInternal(),
        ]);
    }

    public function tambahStok(Request $request): JsonResponse
    {
        $data = $request->validate([
            'jumlah'     => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $keterangan = trim($data['keterangan'] ?? '');
        if ($keterangan === '') {
            $keterangan = 'Penambahan stok kurban';
        }

        $updated = $this->tambahStokInternal($data['jumlah'], $keterangan, 'masuk');

        return response()->json([
            'success' => true,
            'message' => "Berhasil menambahkan {$data['jumlah']} paket stok. Sisa stok sekarang: {$updated['stok_tersedia']} paket.",
            'data'    => $updated,
        ]);
    }

    public function aturStok(Request $request): JsonResponse
    {
        $data = $request->validate([
            'stok_tersedia' => ['required', 'integer', 'min:0'],
            'keterangan'    => ['nullable', 'string', 'max:255'],
        ]);

        $updated = $this->aturStokInternal($data['stok_tersedia'], $data['keterangan'] ?? null);

        return response()->json([
            'success' => true,
            'message' => "Stok berhasil disetel menjadi {$data['stok_tersedia']} paket.",
            'data'    => $updated,
        ]);
    }
}