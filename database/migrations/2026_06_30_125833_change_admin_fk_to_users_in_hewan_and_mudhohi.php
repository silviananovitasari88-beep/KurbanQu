<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hewan')) {
            Schema::create('hewan', function (Blueprint $table) {
                $table->increments('id_hewan');
                $table->enum('jenis', ['Sapi', 'Domba', 'Kambing'])->nullable();
                $table->enum('sehat', ['Sehat', 'Tidak Sehat'])->nullable();
                $table->enum('cacat', ['Cacat', 'Tidak Cacat'])->nullable();
                $table->string('umur', 50)->nullable();
                $table->string('berat', 50)->nullable();
                $table->boolean('st_syariat')->nullable();
                $table->unsignedBigInteger('admin_id_admin');
                $table->unsignedInteger('tracking_id_tracking')->nullable();

                $table->foreign('admin_id_admin')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('mudhohi')) {
            Schema::create('mudhohi', function (Blueprint $table) {
                $table->increments('id_mudhohi');
                $table->string('nama_mudhohi', 45)->nullable();
                $table->string('nama_ayah', 45)->nullable();
                $table->string('alamat', 100)->nullable();
                $table->string('notelp_mudhohi', 20)->nullable();
                $table->string('req_bagian', 45)->nullable();
                $table->unsignedBigInteger('admin_id_admin');
                $table->unsignedInteger('hewan_id_hewan');

                $table->foreign('admin_id_admin')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();

                $table->foreign('hewan_id_hewan')
                    ->references('id_hewan')
                    ->on('hewan')
                    ->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mudhohi');
        Schema::dropIfExists('hewan');
    }
};