<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Warga; // sesuaikan namespace model Warga Anda

class PenerimaController extends Controller
{
    // ... method lain yang sudah ada ...

    /**
     * Menyimpan atau memperbarui data penerima dari request JSON.
     */
    public function simpanPenerima(Request $request)
    {
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

            if ($mode === 'replace') {
                Warga::truncate();
            }

            $created = 0;
            $updated = 0;
            $failed = 0;
            $errors = [];
            $nextId = 1;

            $maxId = Warga::max('id_penerima') ?? 0;
            $nextId = $maxId + 1;

            foreach ($penerima as $idx => $row) {
                try {
                    $nkk = preg_replace('/\D/', '', $row['nkk'] ?? '');
                    $nama = trim($row['nama'] ?? '');

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

                    $exists = DB::table('warga')->where('no_kk', $nkk)->exists();

                    if ($exists) {
                        $updateData = [
                            'nama_kk'     => $nama,
                            'alamat'      => trim($row['alamat'] ?? ''),
                            'no_telp'     => trim($row['notelp'] ?? ''),
                        ];
                        $updateData['QR_id_qr'] = !empty($row['qrCode']) ? null : null;
                        DB::table('warga')->where('no_kk', $nkk)->update($updateData);

                        $distExists = DB::table('distribusi')
                            ->where('warga_no_kk', $nkk)
                            ->exists();

                        if (!$distExists) {
                            DB::table('distribusi')->insert([
                                'warga_no_kk'     => $nkk,
                                'st_pengambilan'  => 'pending',
                                'mtd_pengambilan' => null,
                                'login'           => 'belum_login',
                                'dowload_qr'      => null,
                                'QR_id_qr'        => null,
                            ]);
                        }

                        $updated++;
                    } else {
                        $idPenerima = $nextId++;
                        $qrCode     = 'P' . str_pad((string) $idPenerima, 5, '0', STR_PAD_LEFT);

                        DB::table('warga')->insert([
                            'no_kk'       => $nkk,
                            'nama_kk'     => $nama,
                            'alamat'      => trim($row['alamat'] ?? ''),
                            'no_telp'     => trim($row['notelp'] ?? ''),
                            'id_penerima' => $idPenerima,
                            'QR_id_qr'    => null,
                        ]);

                        $distribusiRow = [
                            'warga_no_kk'     => $nkk,
                            'st_pengambilan'  => 'pending',
                            'mtd_pengambilan' => null,
                            'login'           => 'belum_login',
                            'dowload_qr'      => null,
                            'QR_id_qr'        => null,
                        ];
                        DB::table('distribusi')->insert($distribusiRow);

                        Log::info('[KurbanQu] Insert distribusi OK untuk NKK: ' . $nkk);
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
    }
}