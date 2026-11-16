<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('stok_kurban')) {
            Schema::create('stok_kurban', function (Blueprint $table) {
                $table->id();
                $table->integer('stok_tersedia')->default(0);
                $table->integer('total_masuk')->default(0);
                $table->integer('total_keluar')->default(0);
                $table->timestamps();
            });

            DB::table('stok_kurban')->insert([
                'id' => 1,
                'stok_tersedia' => 0,
                'total_masuk' => 0,
                'total_keluar' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!Schema::hasTable('stok_log')) {
            Schema::create('stok_log', function (Blueprint $table) {
                $table->id();
                $table->string('tipe', 30)->default('masuk'); // masuk, keluar, batal, penyesuaian
                $table->integer('jumlah');
                $table->integer('stok_sebelum');
                $table->integer('stok_sesudah');
                $table->string('keterangan')->nullable();
                $table->string('referensi')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_log');
        Schema::dropIfExists('stok_kurban');
    }
};

