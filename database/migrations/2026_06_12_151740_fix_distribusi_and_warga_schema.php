<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qr', function (Blueprint $table) {
            $table->increments('id_qr');
            $table->unsignedInteger('no_antrian')->nullable();
            $table->integer('dur_sesi')->nullable();
            $table->string('loc_pengambilan', 45)->nullable();
            $table->string('jam_pengambilan', 45)->nullable();
        });

        Schema::create('warga', function (Blueprint $table) {
            $table->string('no_kk', 20)->primary();
            $table->string('nama_kk', 45)->nullable();
            $table->string('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->integer('id_penerima')->nullable()->unique();
            $table->unsignedInteger('QR_id_qr')->nullable();

            $table->foreign('QR_id_qr')
                ->references('id_qr')
                ->on('qr')
                ->nullOnDelete();
        });

        Schema::create('distribusi', function (Blueprint $table) {
            $table->increments('id_stok');
            $table->string('dowload_qr', 50)->nullable();
            $table->unsignedInteger('QR_id_qr')->nullable();
            $table->string('st_pengambilan', 45)->nullable();
            $table->enum('mtd_pengambilan', ['QR', 'Manual', 'manual_admin'])->nullable();
            $table->string('warga_no_kk', 20);
            $table->string('login', 20)->default('belum_login');

            $table->foreign('QR_id_qr')
                ->references('id_qr')
                ->on('qr')
                ->nullOnDelete();

            $table->foreign('warga_no_kk')
                ->references('no_kk')
                ->on('warga')
                ->restrictOnDelete();
        });

        Schema::create('tracking', function (Blueprint $table) {
            $table->increments('id_tracking');
            $table->enum('st_tracking', ['Penyembelihan', 'Pengulitan', 'Pencacahan', 'Penimbangan', 'Pendistribusian'])->nullable();
            $table->timestamp('time_tracking')->nullable();
            $table->unsignedInteger('distribusi_id_stok');

            $table->foreign('distribusi_id_stok')
                ->references('id_stok')
                ->on('distribusi')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking');
        Schema::dropIfExists('distribusi');
        Schema::dropIfExists('warga');
        Schema::dropIfExists('qr');
    }
};
