<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PeriodeKurban;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Semua hal yang bergantung pada PERIODE (tahun kurban):
 * hewan, mudhohi, penerima, distribusi, rekap tahunan, mulai periode baru, dan riwayat perubahan.
 */
class PeriodeController extends Controller
{
    // ───────────────────────── helper ─────────────────────────

    private function periodeAktifId(): ?int
    {
        $id = PeriodeKurban::where('is_aktif', true)->value('id');
        return $id ? (int) $id : null;
    }

    private function alasan(Request $request): string
    {
        return trim(rawurldecode((string) $request->header('X-Alasan', '')));
    }

    private function tolakTanpaAlasan(string $alasan): ?JsonResponse
    {
        if ($alasan === '') {
            return response()->json([
                'success' => false,
                'message' => 'Alasan wajib diisi supaya tercatat di riwayat perubahan.',
            ], 422);
        }
        return null;
    }

    private function tolakArsip(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Data periode lama (arsip) hanya bisa dilihat, tidak bisa diubah.',
        ], 403);
    }

    private function catat(string $aksi, ?string $tabel, $dataId, ?int $periodeId, $lama = null, $baru = null, ?string $alasan = null): void
    {
        try {
            $u = auth()->user();
            $aktor = $u
                ? ($u->name ?? $u->username ?? $u->nama ?? ('user#' . $u->getKey()))
                : 'sistem';

            AuditLog::create([
                'aktor'      => mb_substr((string) $aktor, 0, 100),
                'aksi'       => $aksi,
                'tabel'      => $tabel,
                'data_id'    => $dataId !== null ? (string) $dataId : null,
                'periode_id' => $periodeId,
                'lama'       => $lama,
                'baru'       => $baru,
                'alasan'     => $alasan !== null ? mb_substr($alasan, 0, 255) : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[KurbanQu] Gagal menulis audit log: ' . $e->getMessage());
        }
    }

    /** Hapus distribusi periode aktif, lalu bersihkan warga dan qr yang tidak dipakai periode mana pun. */
    private function hapusDistribusiPeriodeAktif(?int $periodeId): int
    {
        $hapus = DB::table('distribusi')->where('periode_id', $periodeId)->delete();

        DB::table('warga')
            ->whereNotIn('no_kk', DB::table('distribusi')->whereNotNull('warga_no_kk')->select('warga_no_kk'))
            ->delete();

        try {
            DB::table('qr')
                ->whereNotIn('id_qr', DB::table('distribusi')->whereNotNull('QR_id_qr')->select('QR_id_qr'))
                ->whereNotIn('id_qr', DB::table('warga')->whereNotNull('QR_id_qr')->select('QR_id_qr'))
                ->delete();
        } catch (\Throwable $e) {
            Log::warning('[KurbanQu] Bersih-bersih qr dilewati: ' . $e->getMessage());
        }

        return (int) $hapus;
    }

    // ───────────────────────── HEWAN ─────────────────────────

    public function getHewan(Request $request): JsonResponse
    {
        $periodeId = $request->query('periode', $this->periodeAktifId());
        $hewan = DB::table('hewan')->where('periode_id', $periodeId)->orderBy('id_hewan')->get();
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

        $periodeId = $this->periodeAktifId();
        if (!$periodeId) {
            return response()->json(['success' => false, 'message' => 'Belum ada periode kurban yang aktif.'], 422);
        }

        $id = DB::table('hewan')->insertGetId([
            'jenis'          => $data['jenis'],
            'sehat'          => $data['sehat'],
            'cacat'          => $data['cacat'],
            'umur'           => $data['umur'] ?? null,
            'berat'          => $data['berat'] ?? null,
            'st_syariat'     => $data['st_syariat'],
            'admin_id_admin' => auth()->id() ?? 1,
            'periode_id'     => $periodeId,
        ]);

        $this->catat('tambah_hewan', 'hewan', $id, $periodeId, null, $data);

        return response()->json([
            'success' => true,
            'message' => 'Hewan berhasil ditambahkan',
            'data'    => ['id_hewan' => $id],
        ]);
    }

    public function deleteHewan(Request $request, int $idHewan): JsonResponse
    {
        $alasan = $this->alasan($request);
        if ($tolak = $this->tolakTanpaAlasan($alasan)) {
            return $tolak;
        }

        $hewan = DB::table('hewan')->where('id_hewan', $idHewan)->first();
        if (!$hewan) {
            return response()->json(['success' => false, 'message' => 'Hewan tidak ditemukan'], 404);
        }
        if ((int) $hewan->periode_id !== (int) $this->periodeAktifId()) {
            return $this->tolakArsip();
        }

        $jumlahMudhohi = DB::table('mudhohi')->where('hewan_id_hewan', $idHewan)->count();

        DB::transaction(function () use ($idHewan) {
            DB::table('mudhohi')->where('hewan_id_hewan', $idHewan)->delete();
            DB::table('hewan')->where('id_hewan', $idHewan)->delete();
        });

        $this->catat(
            'hapus_hewan', 'hewan', $idHewan, (int) $hewan->periode_id,
            (array) $hewan, ['mudhohi_ikut_terhapus' => $jumlahMudhohi], $alasan
        );

        return response()->json(['success' => true, 'message' => 'Hewan berhasil dihapus']);
    }

    // ───────────────────────── MUDHOHI ─────────────────────────

    public function getMudhohi(Request $request): JsonResponse
    {
        $periodeId = $request->query('periode', $this->periodeAktifId());

        $mudhohi = DB::table('mudhohi')
            ->join('hewan', 'hewan.id_hewan', '=', 'mudhohi.hewan_id_hewan')
            ->where('hewan.periode_id', $periodeId)
            ->select('mudhohi.*')
            ->orderBy('mudhohi.id_mudhohi')
            ->get();

        return response()->json(['success' => true, 'data' => $mudhohi]);
    }

    // ───────────────────────── DISTRIBUSI & PENERIMA ─────────────────────────

    public function distribusiSnapshot(Request $request): JsonResponse
    {
        $periodeId = $request->query('periode', $this->periodeAktifId());

        $rows = DB::table('distribusi as d')
            ->where('d.periode_id', $periodeId)
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
                'id_stok'         => $row->id_stok,
                'warga_no_kk'     => $key,
                'qr_id_qr'        => $row->QR_id_qr,
                'nama_kk'         => $row->nama_kk,
                'id_penerima'     => $row->id_penerima,
                'dowload_qr'      => $row->dowload_qr,
                'st_pengambilan'  => $row->st_pengambilan,
                'mtd_pengambilan' => $row->mtd_pengambilan,
                'login'           => $row->login,
                'status_login'    => $row->status_login,
                'updated_at'      => null,
                'jam_pengambilan' => $row->jam_pengambilan,
            ];
        }

        return response()->json(['success' => true, 'data' => $mapped]);
    }

    public function getPenerimaList(Request $request): JsonResponse
    {
        try {
            $periodeId = $request->query('periode', $this->periodeAktifId());

            $kkPeriode = DB::table('distribusi')
                ->where('periode_id', $periodeId)
                ->whereNotNull('warga_no_kk')
                ->select('warga_no_kk');

            $warga = DB::table('warga')
                ->whereIn('no_kk', $kkPeriode)
                ->select('no_kk', 'nama_kk', 'alamat', 'no_telp', 'id_penerima', 'QR_id_qr')
                ->orderBy('id_penerima', 'asc')
                ->get();

            $result = $warga->map(function ($item) use ($periodeId) {
                $dist = DB::table('distribusi')
                    ->where('warga_no_kk', $item->no_kk)
                    ->where('periode_id', $periodeId)
                    ->first();

                $status = 'BELUM AMBIL';
                if ($dist && in_array($dist->st_pengambilan, ['diambil', 'selesai'], true)) {
                    $status = 'SUDAH AMBIL';
                }

                return [
                    'no_kk'       => $item->no_kk,
                    'nama_kk'     => $item->nama_kk ?? '-',
                    'alamat'      => $item->alamat ?? '-',
                    'no_telp'     => $item->no_telp ?? '-',
                    'id_penerima' => $item->id_penerima,
                    'qr_code'     => $item->QR_id_qr ?? 'P' . str_pad($item->id_penerima ?? 0, 5, '0', STR_PAD_LEFT),
                    'status'      => $status,
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $result,
                'total'   => $result->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getPenerimaList: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteWarga(Request $request, $noKk): JsonResponse
    {
        $alasan = $this->alasan($request);
        if ($tolak = $this->tolakTanpaAlasan($alasan)) {
            return $tolak;
        }

        $periodeId = $this->periodeAktifId();
        $warga = DB::table('warga')->where('no_kk', $noKk)->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Data warga dengan No KK ' . $noKk . ' tidak ditemukan',
            ], 404);
        }

        try {
            $hasil = DB::transaction(function () use ($noKk, $periodeId) {
                // hanya distribusi periode aktif yang dihapus, arsip tahun lain aman
                $hapusDist = DB::table('distribusi')
                    ->where('warga_no_kk', $noKk)
                    ->where('periode_id', $periodeId)
                    ->delete();

                // data warga baru dihapus kalau tidak punya distribusi di periode mana pun
                $punyaArsip = DB::table('distribusi')->where('warga_no_kk', $noKk)->exists();
                $hapusWarga = 0;
                if (!$punyaArsip) {
                    $hapusWarga = DB::table('warga')->where('no_kk', $noKk)->delete();
                }

                return ['distribusi' => $hapusDist, 'warga' => $hapusWarga];
            });

            $this->catat('hapus_warga', 'warga', $noKk, $periodeId, (array) $warga, $hasil, $alasan);

            return response()->json([
                'success' => true,
                'message' => "Data penerima '{$warga->nama_kk}' berhasil dihapus dari periode ini",
                'data'    => [
                    'no_kk'              => $noKk,
                    'nama'               => $warga->nama_kk,
                    'deleted_distribusi' => $hasil['distribusi'],
                    'deleted_warga'      => $hasil['warga'],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal hapus warga: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }

    public function clearPenerimaData(Request $request): JsonResponse
    {
        $alasan = $this->alasan($request);
        if ($tolak = $this->tolakTanpaAlasan($alasan)) {
            return $tolak;
        }

        $periodeId = $this->periodeAktifId();
        $jumlah = 0;

        DB::transaction(function () use ($periodeId, &$jumlah) {
            $jumlah = $this->hapusDistribusiPeriodeAktif($periodeId);
        });

        $this->catat('hapus_semua_penerima', 'distribusi', null, $periodeId, null, ['distribusi_terhapus' => $jumlah], $alasan);

        return response()->json([
            'success' => true,
            'message' => 'Semua data penerima periode ini berhasil dihapus. Arsip tahun lain aman.',
            'data'    => ['deleted_distribusi' => $jumlah],
        ]);
    }

    public function simpanPenerima(Request $request): JsonResponse
    {
        $request->validate([
            'penerima'         => 'required|array|min:1',
            'penerima.*.nkk'   => 'required|string|min:6',
            'penerima.*.nama'  => 'required|string|min:2',
        ], [
            'penerima.required'        => 'Data penerima tidak boleh kosong',
            'penerima.*.nkk.required'  => 'No KK wajib diisi',
            'penerima.*.nama.required' => 'Nama wajib diisi',
        ]);

        $periodeId = $this->periodeAktifId();
        if (!$periodeId) {
            return response()->json(['success' => false, 'message' => 'Belum ada periode kurban yang aktif.'], 422);
        }

        $penerima = $request->input('penerima', []);
        $mode = $request->input('mode', 'append');

        try {
            DB::beginTransaction();

            if ($mode === 'replace') {
                // hanya periode aktif yang diganti, arsip tahun lain aman
                $this->hapusDistribusiPeriodeAktif($periodeId);
            }

            $created = 0;
            $updated = 0;
            $failed = 0;
            $errors = [];

            $nextId = ((int) (DB::table('warga')->max('id_penerima') ?? 0)) + 1;

            foreach ($penerima as $idx => $row) {
                try {
                    $nkk  = preg_replace('/\D/', '', $row['nkk'] ?? '');
                    $nama = trim($row['nama'] ?? '');

                    if (strlen($nkk) < 10) {
                        $errors[] = 'Baris ' . ($idx + 1) . ": No KK '{$row['nkk']}' kurang dari 10 digit";
                        $failed++;
                        continue;
                    }
                    if (strlen($nama) < 2) {
                        $errors[] = 'Baris ' . ($idx + 1) . ': Nama tidak boleh kosong';
                        $failed++;
                        continue;
                    }

                    $distribusiRow = [
                        'warga_no_kk'     => $nkk,
                        'st_pengambilan'  => 'pending',
                        'mtd_pengambilan' => null,
                        'login'           => 'belum_login',
                        'dowload_qr'      => null,
                        'QR_id_qr'        => null,
                        'periode_id'      => $periodeId,
                    ];

                    $exists = DB::table('warga')->where('no_kk', $nkk)->exists();

                    if ($exists) {
                        $updateData = [
                            'nama_kk'  => $nama,
                            'alamat'   => trim($row['alamat'] ?? ''),
                            'no_telp'  => trim($row['notelp'] ?? ''),
                            'QR_id_qr' => null,
                        ];
                        if (!empty($row['id_penerima'])) {
                            $updateData['id_penerima'] = $row['id_penerima'];
                        }
                        DB::table('warga')->where('no_kk', $nkk)->update($updateData);

                        // satu baris distribusi per warga per periode
                        $distExists = DB::table('distribusi')
                            ->where('warga_no_kk', $nkk)
                            ->where('periode_id', $periodeId)
                            ->exists();

                        if (!$distExists) {
                            DB::table('distribusi')->insert($distribusiRow);
                        }
                        $updated++;
                    } else {
                        $idPenerima = !empty($row['id_penerima']) ? $row['id_penerima'] : $nextId++;

                        DB::table('warga')->insert([
                            'no_kk'       => $nkk,
                            'nama_kk'     => $nama,
                            'alamat'      => trim($row['alamat'] ?? ''),
                            'no_telp'     => trim($row['notelp'] ?? ''),
                            'id_penerima' => $idPenerima,
                            'QR_id_qr'    => null,
                        ]);

                        DB::table('distribusi')->insert($distribusiRow);
                        $created++;
                    }
                } catch (\Exception $e) {
                    $errors[] = 'Baris ' . ($idx + 1) . ': ' . $e->getMessage();
                    $failed++;
                }
            }

            DB::commit();

            $this->catat(
                'impor_penerima', 'distribusi', null, $periodeId, null,
                ['mode' => $mode, 'baru' => $created, 'diperbarui' => $updated, 'gagal' => $failed],
                $this->alasan($request) ?: null
            );

            return response()->json([
                'success' => true,
                'message' => "✅ {$created} penerima baru, {$updated} diperbarui" . ($failed > 0 ? ", {$failed} gagal" : ''),
                'data'    => [
                    'created' => $created,
                    'updated' => $updated,
                    'failed'  => $failed,
                    'total'   => $created + $updated,
                    'errors'  => $errors,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => '❌ Gagal menyimpan penerima: ' . $e->getMessage(),
                'data'    => null,
            ], 422);
        }
    }

    // ───────────────────────── REKAP & PERIODE ─────────────────────────

    public function rekapTahunan(): JsonResponse
    {
        $data = PeriodeKurban::orderBy('tahun')->get()->map(function ($p) {
            $jenis = DB::table('hewan')
                ->where('periode_id', $p->id)
                ->pluck('jenis')
                ->map(fn ($j) => strtolower((string) $j));

            $penerima = DB::table('distribusi')->where('periode_id', $p->id)->count();
            $diambil  = DB::table('distribusi')
                ->where('periode_id', $p->id)
                ->whereIn('st_pengambilan', ['selesai', 'diambil'])
                ->count();

            return [
                'id'          => $p->id,
                'tahun'       => (string) $p->tahun,
                'hijriah'     => $p->hijriah,
                'aktif'       => (bool) $p->is_aktif,
                'sapi'        => $jenis->filter(fn ($j) => $j === 'sapi')->count(),
                'kambing'     => $jenis->filter(fn ($j) => $j === 'kambing')->count(),
                'domba'       => $jenis->filter(fn ($j) => $j === 'domba')->count(),
                'total_hewan' => $jenis->count(),
                'penerima'    => $penerima,
                'diambil'     => $diambil,
            ];
        })->values();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function mulaiPeriodeBaru(Request $request): JsonResponse
    {
        $alasan = $this->alasan($request);
        if ($tolak = $this->tolakTanpaAlasan($alasan)) {
            return $tolak;
        }

        $data = $request->validate([
            'tahun'   => ['required', 'integer', 'min:2000', 'max:2200', 'unique:periode_kurban,tahun'],
            'hijriah' => ['nullable', 'string', 'max:20'],
        ]);

        $baru = null;

        DB::transaction(function () use ($data, $alasan, &$baru) {
            $lama = PeriodeKurban::where('is_aktif', true)->first();

            $stokLama = null;
            if (Schema::hasTable('stok_kurban')) {
                $stokLama = DB::table('stok_kurban')->where('id', 1)->first();
            }

            PeriodeKurban::query()->update(['is_aktif' => false]);
            $baru = PeriodeKurban::create([
                'tahun'    => $data['tahun'],
                'hijriah'  => $data['hijriah'] ?? null,
                'is_aktif' => true,
            ]);

            // Status proses (tracking) mulai dari awal lagi
            if (Schema::hasTable('tracking_steps')) {
                try {
                    DB::table('tracking_steps')->update(['status' => 'pending', 'time' => null]);
                } catch (\Throwable $e) {
                    DB::table('tracking_steps')->update(['status' => 'pending']);
                }
            }

            // Stok daging mulai dari nol; angka periode lama dicatat di riwayat
            if ($stokLama) {
                DB::table('stok_kurban')->where('id', 1)->update([
                    'stok_tersedia' => 0,
                    'total_masuk'   => 0,
                    'total_keluar'  => 0,
                    'updated_at'    => now(),
                ]);

                if (Schema::hasTable('stok_log')) {
                    try {
                        DB::table('stok_log')->insert([
                            'tipe'         => 'penyesuaian',
                            'jumlah'       => (int) $stokLama->stok_tersedia,
                            'stok_sebelum' => (int) $stokLama->stok_tersedia,
                            'stok_sesudah' => 0,
                            'keterangan'   => 'Reset stok untuk periode ' . $data['tahun'],
                            'referensi'    => null,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    } catch (\Throwable $e) {
                        // riwayat stok opsional
                    }
                }
            }

            // QR warga dibuat ulang untuk periode baru
            DB::table('warga')->update(['QR_id_qr' => null]);

            $this->catat(
                'mulai_periode', 'periode_kurban', $baru->id, (int) $baru->id,
                $lama ? ['tahun' => $lama->tahun, 'hijriah' => $lama->hijriah] : null,
                [
                    'tahun'   => $data['tahun'],
                    'hijriah' => $data['hijriah'] ?? null,
                    'stok_periode_lama' => $stokLama ? [
                        'tersedia' => (int) $stokLama->stok_tersedia,
                        'masuk'    => (int) $stokLama->total_masuk,
                        'keluar'   => (int) $stokLama->total_keluar,
                    ] : null,
                ],
                $alasan
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Periode ' . $data['tahun'] . ' dimulai. Data periode sebelumnya tersimpan sebagai arsip.',
            'data'    => ['id' => $baru->id, 'tahun' => $baru->tahun],
        ]);
    }

    // ───────────────────────── RIWAYAT PERUBAHAN ─────────────────────────

    public function daftarAudit(Request $request): JsonResponse
    {
        $limit = max(1, min(200, (int) $request->query('limit', 50)));

        $rows = DB::table('audit_log')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function ($r) {
                $r->lama = $r->lama ? json_decode($r->lama, true) : null;
                $r->baru = $r->baru ? json_decode($r->baru, true) : null;
                return $r;
            });

        return response()->json(['success' => true, 'data' => $rows]);
    }
}
