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
        // Drop legacy tracking table if exists
        Schema::dropIfExists('tracking');

        // Drop non-standard warga_uploads table if exists
        Schema::dropIfExists('warga_uploads');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('tracking')) {
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
    }
};
