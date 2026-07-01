<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
	public function distribusiSnapshot(): JsonResponse
	{
		$rows = DB::table('distribusi as d')
			->leftJoin('warga as w', 'w.no_kk', '=', 'd.warga_no_kk')
			->leftJoin('QR as q', 'q.id_qr', '=', 'd.QR_id_qr')
			->select([
				'd.id_stok',
				'd.warga_no_kk',
				'd.QR_id_qr',
				'd.dowload_qr',
				'd.st_pengambilan',
				'd.mtd_pengambilan',
				'd.login',
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

		if (Schema::hasTable('QR') && Schema::hasColumn('QR', 'jam_pengambilan')) {
			$qrId = $data['qr_id_qr'] ?? $row->QR_id_qr ?? null;
			if ($qrId !== null) {
				DB::table('QR')->where('id_qr', $qrId)->update([
					'jam_pengambilan' => $now,
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
        'mtd_pengambilan' => $metode, // ← pakai variabel
        'updated_at' => $now->toDateTimeString(),
    ],
]);
	}

    public function clearAllPenerima(): JsonResponse
{
    try {
        // Hapus semua data distribusi dulu (karena ada foreign key ke warga)
        DB::table('distribusi')->delete();

        // Hapus semua data warga (penerima kurban)
        DB::table('warga')->delete();

        // Reset auto increment di SQLite (opsional)
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
			foreach (['distribusi', 'warga', 'QR'] as $table) {
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
    $header = array_shift($rows); // baris pertama = header

    DB::transaction(function () use ($rows) {
        foreach ($rows as $row) {
            if (count($row) < 3) continue;

            // Sesuaikan index kolom dengan format CSV Anda
            $noKk    = trim($row[0]);
            $namaKk  = trim($row[1]);
            $alamat  = trim($row[2] ?? '');

            // 1. Insert ke tabel warga
            $idPenerima = DB::table('warga')->insertGetId([
                'no_kk'   => $noKk,
                'nama_kk' => $namaKk,
                'alamat'  => $alamat,
            ]);

            // 2. Insert QR
            $idQr = DB::table('QR')->insertGetId([
                'no_antrian'     => $idPenerima,
                'loc_pengambilan' => 'Lokasi Pengambilan',
                'dur_sesi'       => 15,
            ]);

            // Update warga dengan QR_id_qr dan id_penerima
            DB::table('warga')->where('no_kk', $noKk)->update([
                'QR_id_qr'    => $idQr,
                'id_penerima' => $idPenerima,
            ]);

            // 3. ← KUNCI: Insert ke distribusi sekaligus
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

    // ═══════════════════════════════════════════
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
}
        