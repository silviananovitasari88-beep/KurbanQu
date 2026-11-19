<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('aktor', 100);                 // siapa yang mengubah
            $table->string('aksi', 60);                   // apa yang dilakukan
            $table->string('tabel', 60)->nullable();      // tabel yang terkena
            $table->string('data_id', 100)->nullable();   // id/no KK data yang terkena
            $table->unsignedBigInteger('periode_id')->nullable();
            $table->json('lama')->nullable();             // nilai sebelum
            $table->json('baru')->nullable();             // nilai sesudah
            $table->string('alasan', 255)->nullable();
            $table->timestamp('created_at')->useCurrent(); // jam server, bukan jam pengguna
            $table->index('created_at');
        });

        // Pengaman di database: baris riwayat tidak bisa diubah atau dihapus
        try {
            DB::unprepared("CREATE TRIGGER audit_log_no_update BEFORE UPDATE ON audit_log FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_log tidak boleh diubah'");
            DB::unprepared("CREATE TRIGGER audit_log_no_delete BEFORE DELETE ON audit_log FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_log tidak boleh dihapus'");
        } catch (\Throwable $e) {
            // Kalau akun database tidak punya izin membuat trigger, pengaman di aplikasi tetap jalan.
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
