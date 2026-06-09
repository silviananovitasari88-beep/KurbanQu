<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WargaQrController extends Controller
{
    public function download(Request $request)
    {
        $data = $request->validate([
            'nkk' => ['required', 'string'],
            'nama' => ['required', 'string'],
        ]);

        $nkk = preg_replace('/\D+/', '', $data['nkk']);
        $nama = trim($data['nama']);
        $normalizedNama = strtolower(preg_replace('/\s+/', ' ', $nama));

        $warga = DB::table('warga')
            ->where('no_kk', $nkk)
            ->first();

        if ($warga && isset($warga->nama_kk)) {
            $storedNama = strtolower(preg_replace('/\s+/', ' ', trim((string) $warga->nama_kk)));
            if ($storedNama !== $normalizedNama) {
                $warga = null;
            }
        }

        if (!$warga) {
            $warga = DB::table('warga')
                ->where('no_kk', $nkk)
                ->whereRaw('LOWER(TRIM(nama_kk)) = ?', [$normalizedNama])
                ->first();
        }

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Data warga tidak ditemukan untuk NKK ' . $nkk . '.',
            ], 404);
        }

        $qrPayload = trim((string) ($warga->QR_id_qr ?? ''));
        if ($qrPayload === '' && isset($warga->id_penerima)) {
            $qrPayload = 'P' . str_pad((string) $warga->id_penerima, 5, '0', STR_PAD_LEFT);
        }

        if ($qrPayload === '') {
            return response()->json([
                'success' => false,
                'message' => 'QR warga belum tersedia.',
            ], 422);
        }

        if (!class_exists(\Imagick::class)) {
            return response()->json([
                'success' => false,
                'message' => 'Extension Imagick belum tersedia pada server.',
            ], 500);
        }

        $svg = $this->buildQrSvg($qrPayload, $nama, $nkk);
        $filePath = $this->renderPngWithImagick($svg, $nkk, $qrPayload);

        DB::table('distribusi')
            ->where('warga_no_kk', $nkk)
            ->update([
                'dowload_qr' => 'sudah_download',
            ]);

        $downloadName = 'qr-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', $qrPayload) . '.png';

        return response()
            ->download($filePath, $downloadName, [
                'Content-Type' => 'image/png',
            ])
            ->deleteFileAfterSend(true);
    }

    private function buildQrSvg(string $qrPayload, string $nama, string $nkk): string
    {
        $size = 320;
        $safeNama = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
        $safeNkk = htmlspecialchars($nkk, ENT_QUOTES, 'UTF-8');
        $safePayload = htmlspecialchars($qrPayload, ENT_QUOTES, 'UTF-8');

        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size($size - 48)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($qrPayload);

            return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . ($size + 96) . '" viewBox="0 0 ' . $size . ' ' . ($size + 96) . '">' .
                '<rect width="100%" height="100%" fill="#ffffff"/>' .
                '<rect x="24" y="24" width="' . ($size - 48) . '" height="' . ($size - 48) . '" rx="28" fill="#f8f5ef" stroke="#e4d5bf" stroke-width="2"/>' .
                '<g transform="translate(24,24)">' . $qrSvg . '</g>' .
                '<text x="50%" y="' . ($size + 32) . '" text-anchor="middle" font-family="Arial, sans-serif" font-size="22" font-weight="700" fill="#3d2510">' . $safeNama . '</text>' .
                '<text x="50%" y="' . ($size + 58) . '" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" fill="#9a8060">NKK ' . $safeNkk . '</text>' .
                '<text x="50%" y="' . ($size + 82) . '" text-anchor="middle" font-family="Arial, sans-serif" font-size="12" fill="#c8922a">' . $safePayload . '</text>' .
            '</svg>';
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . ($size + 96) . '" viewBox="0 0 ' . $size . ' ' . ($size + 96) . '">' .
            '<rect width="100%" height="100%" fill="#ffffff"/>' .
            '<rect x="24" y="24" width="' . ($size - 48) . '" height="' . ($size - 48) . '" rx="28" fill="#f8f5ef" stroke="#e4d5bf" stroke-width="2"/>' .
            '<text x="50%" y="' . (($size / 2) + 10) . '" text-anchor="middle" font-family="Arial, sans-serif" font-size="20" font-weight="700" fill="#3d2510">' . $safePayload . '</text>' .
            '<text x="50%" y="' . ($size + 32) . '" text-anchor="middle" font-family="Arial, sans-serif" font-size="22" font-weight="700" fill="#3d2510">' . $safeNama . '</text>' .
            '<text x="50%" y="' . ($size + 58) . '" text-anchor="middle" font-family="Arial, sans-serif" font-size="14" fill="#9a8060">NKK ' . $safeNkk . '</text>' .
            '</svg>';
    }

    private function renderPngWithImagick(string $svg, string $nkk, string $payload): string
    {
        $tmpDir = storage_path('app/tmp/qr-downloads');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0775, true);
        }

        $fileName = 'qr-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', $payload) . '-' . $nkk . '.png';
        $filePath = $tmpDir . DIRECTORY_SEPARATOR . $fileName;

        $imagick = new \Imagick();
        $imagick->setBackgroundColor(new \ImagickPixel('white'));
        $imagick->setResolution(600, 600);
        $imagick->readImageBlob($svg);
        $imagick->setImageFormat('png');
        $imagick->setImageCompressionQuality(100);
        $imagick->writeImage($filePath);
        $imagick->clear();
        $imagick->destroy();

        return $filePath;
    }
}