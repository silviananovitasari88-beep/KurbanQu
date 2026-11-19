<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Supaya warga yang sama bisa punya satu baris distribusi PER TAHUN.
 * Kalau di tabel distribusi ada unique index pada warga_no_kk atau QR_id_qr,
 * index itu diganti index biasa, lalu dibuat unique gabungan (warga_no_kk + periode_id).
 * Semua langkah dibungkus try/catch: kalau tidak berlaku di database kamu, dilewati.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::select("
            SELECT INDEX_NAME AS idx, MIN(COLUMN_NAME) AS col
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'distribusi'
              AND NON_UNIQUE = 0
              AND INDEX_NAME <> 'PRIMARY'
            GROUP BY INDEX_NAME
            HAVING COUNT(*) = 1
        ");

        foreach ($rows as $r) {
            if (!in_array($r->col, ['warga_no_kk', 'QR_id_qr'], true)) {
                continue;
            }
            try {
                $plain = 'distribusi_' . strtolower($r->col) . '_idx';
                DB::statement("ALTER TABLE `distribusi` ADD INDEX `{$plain}` (`{$r->col}`)");
                DB::statement("ALTER TABLE `distribusi` DROP INDEX `{$r->idx}`");
            } catch (\Throwable $e) {
                // dilewati
            }
        }

        try {
            Schema::table('distribusi', function (Blueprint $table) {
                $table->unique(['warga_no_kk', 'periode_id'], 'distribusi_warga_periode_unique');
            });
        } catch (\Throwable $e) {
            // dilewati
        }
    }

    public function down(): void
    {
        try {
            Schema::table('distribusi', function (Blueprint $table) {
                $table->dropUnique('distribusi_warga_periode_unique');
            });
        } catch (\Throwable $e) {
            // dilewati
        }
    }
};
