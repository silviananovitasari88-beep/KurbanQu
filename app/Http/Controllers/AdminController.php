<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

// Batalkan pengambilan (set status kembali ke pending)
public function batalkanDistribusi(Request $request, $idStok)
{
    DB::table('distribusi')
        ->where('id_stok', $idStok)
        ->update([
            'st_pengambilan'  => 'pending',
            'mtd_pengambilan' => null,
        ]);

    return response()->json(['success' => true]);
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

		$updateData = [
    		'st_pengambilan'  => 'selesai',
    		'mtd_pengambilan' => $request->input('metode') === 'QR' ? 'QR' : 'Manual',
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

		$metode = $request->input('metode') === 'QR' ? 'QR' : 'Manual';
		return response()->json([
		    'success' => true,
		    'message' => 'Status distribusi diperbarui.',
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
	        DB::table('warga')->delete();
	        DB::statement('ALTER TABLE warga AUTO_INCREMENT = 1');
	        DB::statement('ALTER TABLE distribusi AUTO_INCREMENT = 1');

	        return response()->json([
	            'success' => true,
	            'message' => 'Semua data penerima berhasil dihapus.',
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
	                'no_antrian'     => $idPenerima,
	                'loc_pengambilan' => 'Lokasi Pengambilan',
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
}