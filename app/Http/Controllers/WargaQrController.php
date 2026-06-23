<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WargaQrController extends Controller
{
    /**
     * Download QR Code sebagai PNG untuk warga penerima.
     * POST /warga/download-qr
     */
    public function download(Request $request)
    {
        // ── 1. Validasi input ────────────────────────────────────────────────
        $data = $request->validate([
            'nkk'  => ['required', 'string'],
            'nama' => ['required', 'string'],
        ]);

        $nkk            = preg_replace('/\D+/', '', $data['nkk']);
        $nama           = trim($data['nama']);
        $normalizedNama = strtolower(preg_replace('/\s+/', ' ', $nama));

        // ── 2. Cari warga di database ────────────────────────────────────────
        $warga = DB::table('warga')
            ->where('no_kk', $nkk)
            ->whereRaw('LOWER(TRIM(nama_kk)) = ?', [$normalizedNama])
            ->first();

        // Update status login & download_qr untuk admin
       // ✅ GANTI bagian update & insert fallback di method download()

if ($warga) {
    $updated = DB::table('distribusi')
        ->where('warga_no_kk', $warga->no_kk)
        ->update([
            'login'        => 'sudah_login',      // ← kolom yang dibaca admin
            'dowload_qr'   => 'sudah_login',
            'status_login' => 'Sudah Login',
        ]);

    // Hanya insert jika memang belum ada (seharusnya sudah ada setelah fix Bug 1)
    if ((int) $updated === 0) {
        DB::table('distribusi')->insert([
            'warga_no_kk'    => $warga->no_kk,
            'st_pengambilan' => 'pending',
            'mtd_pengambilan' => null,
            'login'          => 'sudah_login',
            'dowload_qr'     => 'sudah_login',
            'status_login'   => 'Sudah Login',
        ]);
    }
}



        // ── 3. Tentukan payload QR — selalu pakai format P00000 ──────────────
        //    JANGAN pakai QR_id_qr (integer FK), pakai id_penerima
        $idPenerima = (int) ($warga->id_penerima ?? 0);
        $qrPayload  = 'P' . str_pad((string) $idPenerima, 5, '0', STR_PAD_LEFT);

        // ── 4. Ambil data QR (no antrian, sesi, lokasi, jam) ─────────────────
        $qrData = null;
        if (!empty($warga->QR_id_qr)) {
            $qrData = DB::table('QR')
                ->where('id_qr', $warga->QR_id_qr)
                ->first();
        }

        $noAntrian      = $qrData->no_antrian    ?? $idPenerima;
        $durSesi        = $qrData->dur_sesi       ?? 15; // menit, default 15
        $locPengambilan = $qrData->loc_pengambilan ?? 'Lokasi Pengambilan';
        $jamPengambilan = $qrData->jam_pengambilan ?? null;

        // Hitung perkiraan jam jika belum ada
        // Misal tracking dimulai jam tertentu, tiap orang 15 menit
        if (!$jamPengambilan) {
            // Perkiraan: urutan * durSesi menit dari jam 08:00
            $baseMinutes    = 8 * 60; // 08:00
            $estimasiMenit  = $baseMinutes + (($noAntrian - 1) * (int) $durSesi);
            $jamH           = intdiv($estimasiMenit, 60) % 24;
            $jamM           = $estimasiMenit % 60;
            $jamPengambilan = sprintf('%02d:%02d WIB (perkiraan)', $jamH, $jamM);
        } else {
            // Format dari DB
            try {
                $dt = new \DateTime($jamPengambilan);
                $jamPengambilan = $dt->format('H:i') . ' WIB';
            } catch (\Throwable $e) {
                $jamPengambilan = (string) $jamPengambilan;
            }
        }

        // ── 5. Cek Imagick tersedia ──────────────────────────────────────────
        if (!extension_loaded('imagick') || !class_exists(\Imagick::class)) {
            $svg = $this->buildQrSvg(
                $qrPayload, $nama, $nkk,
                $noAntrian, $durSesi, $locPengambilan, $jamPengambilan
            );
            $downloadName = 'qr-kurban-' . $qrPayload . '.svg';
            return response($svg, 200, [
                'Content-Type'        => 'image/svg+xml',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                'Cache-Control'       => 'no-store',
            ]);
        }

        // ── 6. Render PNG via Imagick ────────────────────────────────────────
        $svg = $this->buildQrSvg(
            $qrPayload, $nama, $nkk,
            $noAntrian, $durSesi, $locPengambilan, $jamPengambilan
        );
        $pngData = $this->renderPngWithImagick($svg);

        // ── 7. Catat status download ─────────────────────────────────────────
DB::table('warga')
    ->where('no_kk', $nkk)
    ->update([
        'last_login_at' => now(),
        'is_online' => true,
    ]);

        $downloadName = 'qr-kurban-' . $qrPayload . '.png';

        return response($pngData, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
            'Content-Length'      => (string) strlen($pngData),
            'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'              => 'no-cache',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Bangun SVG kartu QR lengkap dengan info antrian & jadwal.
     */
    private function buildQrSvg(
        string $qrPayload,
        string $nama,
        string $nkk,
        $noAntrian,
        $durSesi,
        string $locPengambilan,
        string $jamPengambilan
    ): string {
        $cardW = 420;
        $cardH = 680;
        $qrSize = 260;
        $qrX = ($cardW - $qrSize) / 2;
        $qrY = 64;

        $safeNama    = htmlspecialchars($nama,           ENT_QUOTES, 'UTF-8');
        $safeNkk     = htmlspecialchars($nkk,            ENT_QUOTES, 'UTF-8');
        $safePayload = htmlspecialchars($qrPayload,      ENT_QUOTES, 'UTF-8');
        $safeLoc     = htmlspecialchars($locPengambilan, ENT_QUOTES, 'UTF-8');
        $safeJam     = htmlspecialchars($jamPengambilan, ENT_QUOTES, 'UTF-8');
        $safeAntrian = htmlspecialchars((string) $noAntrian, ENT_QUOTES, 'UTF-8');
        $safeDur     = htmlspecialchars((string) $durSesi,   ENT_QUOTES, 'UTF-8');

        // ── QR Code inner ────────────────────────────────────────────────────
        $qrInner = '';
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            try {
                $rawSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size($qrSize)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($qrPayload);

                $rawSvg = preg_replace('/<\?xml[^?]*\?>\s*/i', '', $rawSvg);
                $rawSvg = preg_replace('/<!DOCTYPE[^>]*>/i', '', $rawSvg);
                $rawSvg = preg_replace(
                    '/<svg([^>]*)>/i',
                    '<g transform="translate(' . $qrX . ',' . $qrY . ')">',
                    $rawSvg, 1
                );
                $rawSvg  = preg_replace('/<\/svg>\s*$/i', '</g>', $rawSvg, 1);
                $qrInner = $rawSvg;
            } catch (\Throwable $e) {
                $qrInner = $this->qrPlaceholder($qrPayload, $qrX, $qrY, $qrSize);
            }
        } else {
            $qrInner = $this->qrPlaceholder($qrPayload, $qrX, $qrY, $qrSize);
        }

        // ── Posisi elemen teks ───────────────────────────────────────────────
        $divY    = $qrY + $qrSize + 18;   // divider
        $namaY   = $divY + 34;            // nama
        $nkkY    = $namaY + 28;           // no kk
        $kodeY   = $nkkY + 24;            // kode QR

        $infoBoxY = $kodeY + 30;          // kotak info antrian
        $infoBoxH = 120;

        $footerY  = $infoBoxY + $infoBoxH + 16; // footer

        // Hitung waktu akhir sesi
        $durInt = (int) $durSesi;

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg"
     width="{$cardW}" height="{$cardH}"
     viewBox="0 0 {$cardW} {$cardH}">

  <!-- Latar kartu -->
  <rect width="{$cardW}" height="{$cardH}" rx="20" ry="20" fill="#fffdf9"/>

  <!-- Header strip emas -->
  <rect width="{$cardW}" height="48" rx="20" ry="20" fill="#c8922a"/>
  <rect x="0" y="26" width="{$cardW}" height="22" fill="#c8922a"/>
  <text x="50%" y="30" text-anchor="middle"
        font-family="Arial, sans-serif" font-size="13" font-weight="700"
        fill="#ffffff" letter-spacing="3">KURBAN QU</text>

  <!-- Kotak putih area QR -->
  <rect x="56" y="56" width="308" height="280" rx="14" ry="14"
        fill="#ffffff" stroke="#e4d5bf" stroke-width="1.5"/>

  <!-- QR Code -->
  {$qrInner}

  <!-- Divider -->
  <line x1="32" y1="{$divY}" x2="388" y2="{$divY}"
        stroke="#e4d5bf" stroke-width="1"/>

  <!-- Nama -->
  <text x="50%" y="{$namaY}"
        text-anchor="middle" font-family="Arial, sans-serif"
        font-size="20" font-weight="700" fill="#3d2510">{$safeNama}</text>

  <!-- No KK -->
  <text x="50%" y="{$nkkY}"
        text-anchor="middle" font-family="Arial, sans-serif"
        font-size="12" fill="#9a8060">No. KK: {$safeNkk}</text>

  <!-- Kode QR -->
  <text x="50%" y="{$kodeY}"
        text-anchor="middle" font-family="Arial, sans-serif"
        font-size="14" font-weight="700" fill="#c8922a"
        letter-spacing="2">{$safePayload}</text>

  <!-- ── Kotak Info Antrian ─────────────────────────────── -->
  <rect x="24" y="{$infoBoxY}" width="372" height="{$infoBoxH}"
        rx="12" ry="12" fill="#fdf6e9" stroke="#e4d5bf" stroke-width="1"/>

  <!-- Baris 1: No Antrian -->
  <text x="44" y="{$infoBoxY}" dy="28"
        font-family="Arial, sans-serif" font-size="11" fill="#9a8060">🔢 No. Antrian</text>
  <text x="388" y="{$infoBoxY}" dy="28"
        text-anchor="end" font-family="Arial, sans-serif"
        font-size="16" font-weight="700" fill="#3d2510">{$safeAntrian}</text>

  <!-- Divider tipis -->
  <line x1="36" y1="{$infoBoxY}" x2="384" y2="{$infoBoxY}"
        stroke="#eddfc0" stroke-width="0.8" transform="translate(0,38)"/>

  <!-- Baris 2: Durasi Sesi -->
  <text x="44" y="{$infoBoxY}" dy="58"
        font-family="Arial, sans-serif" font-size="11" fill="#9a8060">⏱ Durasi Sesi</text>
  <text x="388" y="{$infoBoxY}" dy="58"
        text-anchor="end" font-family="Arial, sans-serif"
        font-size="13" font-weight="600" fill="#3d2510">{$safeDur} menit</text>

  <!-- Baris 3: Lokasi -->
  <text x="44" y="{$infoBoxY}" dy="78"
        font-family="Arial, sans-serif" font-size="11" fill="#9a8060">📍 Lokasi</text>
  <text x="388" y="{$infoBoxY}" dy="78"
        text-anchor="end" font-family="Arial, sans-serif"
        font-size="12" font-weight="600" fill="#3d2510">{$safeLoc}</text>

  <!-- Baris 4: Jam -->
  <text x="44" y="{$infoBoxY}" dy="100"
        font-family="Arial, sans-serif" font-size="11" fill="#9a8060">🕐 Perkiraan Jam</text>
  <text x="388" y="{$infoBoxY}" dy="100"
        text-anchor="end" font-family="Arial, sans-serif"
        font-size="12" font-weight="700" fill="#c8922a">{$safeJam}</text>

  <!-- Footer -->
  <rect x="0" y="{$footerY}" width="{$cardW}" height="44" fill="#f3ead8"/>
  <rect x="0" y="{$footerY}" width="{$cardW}" height="44" rx="20" ry="20" fill="#f3ead8"/>
  <rect x="0" y="{$footerY}" width="{$cardW}" height="22" fill="#f3ead8"/>
  <text x="50%" y="{$footerY}" dy="28"
        text-anchor="middle" font-family="Arial, sans-serif"
        font-size="11" fill="#9a8060">Tunjukkan kartu ini saat pengambilan kurban</text>
</svg>
SVG;
    }

    private function qrPlaceholder(string $payload, float $x, float $y, int $size): string
    {
        $safe  = htmlspecialchars($payload, ENT_QUOTES, 'UTF-8');
        $textY = $y + $size / 2 + 8;
        return <<<SVG
<rect x="{$x}" y="{$y}" width="{$size}" height="{$size}" rx="8" fill="#f0ebe2"/>
<text x="50%" y="{$textY}" text-anchor="middle"
      font-family="monospace" font-size="18" font-weight="700"
      fill="#3d2510">{$safe}</text>
SVG;
    }

    private function renderPngWithImagick(string $svg): string
    {
        $imagick = new \Imagick();
        $imagick->setBackgroundColor(new \ImagickPixel('white'));
        $imagick->setResolution(144, 144);
        $imagick->readImageBlob($svg);
        $imagick->setImageFormat('png');
        $imagick->setImageCompressionQuality(95);
        $imagick->setImageBackgroundColor(new \ImagickPixel('white'));
        $imagick->flattenImages();

        $pngData = $imagick->getImageBlob();
        $imagick->clear();
        $imagick->destroy();

        return $pngData;
    

    }  // ← penutup renderPngWithImagick

    public function login(Request $request)

    $data = $request->validate([
        'nkk'  => ['required', 'string'],
        'nama' => ['required', 'string'],
    ]);

    $nkk  = preg_replace('/\D+/', '', $data['nkk']);
    $nama = strtolower(preg_replace('/\s+/', ' ', trim($data['nama'])));

    $warga = DB::table('warga')
        ->where('no_kk', $nkk)
        ->whereRaw('LOWER(TRIM(nama_kk)) = ?', [$nama])
        ->first();

    if (!$warga) {
        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan. Pastikan No. KK dan Nama sesuai.',
        ], 404);
    }

    // Update status login
    $updated = DB::table('distribusi')
        ->where('warga_no_kk', $nkk)
        ->update([
            'login'      => 'sudah_login',
            'dowload_qr' => 'sudah_login',
        ]);

    // Fallback jika baris distribusi belum ada
    if ((int) $updated === 0) {
        DB::table('distribusi')->insert([
            'warga_no_kk'    => $nkk,
            'st_pengambilan' => 'pending',
            'login'          => 'sudah_login',
            'dowload_qr'     => 'sudah_login',
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Login berhasil',
        'data' => [
            'nama'  => $warga->nama_kk,
            'nkk'   => $nkk,
            'login' => 'sudah_login',
        ]
    ]);
}
}