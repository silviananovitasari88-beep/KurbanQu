<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Hapus FK lama yang nunjuk ke tabel admin ──────────────────
        Schema::table('hewan', function (Blueprint $table) {
            $table->dropForeign('fk_hewan_admin');
        });
        Schema::table('mudhohi', function (Blueprint $table) {
            $table->dropForeign('fk_mudhohi_admin1');
        });

        // ── Pastikan kolom admin_id_admin bisa nampung id user (bigint unsigned) ──
        Schema::table('hewan', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id_admin')->change();
        });
        Schema::table('mudhohi', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id_admin')->change();
        });

        // ── Tambah FK baru, nunjuk ke tabel users ──────────────────────
        Schema::table('hewan', function (Blueprint $table) {
            $table->foreign('admin_id_admin')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('mudhohi', function (Blueprint $table) {
            $table->foreign('admin_id_admin')->references('id')->on('users')->onDelete('cascade');
        });

        // ── Hapus tabel admin yang sudah tidak dipakai ─────────────────
        Schema::dropIfExists('admin');
    }

    public function down(): void
    {
        // Buat ulang tabel admin kalau rollback
        Schema::create('admin', function (Blueprint $table) {
            $table->id('id_admin');
            $table->string('nama_adm', 45)->nullable();
            $table->string('pw_adm', 45)->nullable();
        });

        Schema::table('hewan', function (Blueprint $table) {
            $table->dropForeign(['admin_id_admin']);
        });
        Schema::table('mudhohi', function (Blueprint $table) {
            $table->dropForeign(['admin_id_admin']);
        });

        Schema::table('hewan', function (Blueprint $table) {
            $table->integer('admin_id_admin')->change();
            $table->foreign('admin_id_admin')->references('id_admin')->on('admin');
        });
        Schema::table('mudhohi', function (Blueprint $table) {
            $table->integer('admin_id_admin')->change();
            $table->foreign('admin_id_admin')->references('id_admin')->on('admin');
        });
    }
};