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
    foreach (['hewan', 'distribusi'] as $nama) {
        Schema::table($nama, function (Blueprint $table) {
            $table->foreignId('periode_id')->nullable()->constrained('periode_kurban');
        });

        // data yang sudah ada masuk periode 2026 (id = 1)
        DB::table($nama)->whereNull('periode_id')->update(['periode_id' => 1]);
    }
}

public function down(): void
{
    foreach (['hewan', 'distribusi'] as $nama) {
        Schema::table($nama, function (Blueprint $table) {
            $table->dropConstrainedForeignId('periode_id');
        });
    }
}
};