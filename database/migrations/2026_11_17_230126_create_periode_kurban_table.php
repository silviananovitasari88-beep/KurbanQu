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
    Schema::create('periode_kurban', function (Blueprint $table) {
        $table->id();
        $table->year('tahun')->unique();      // contoh: 2026
        $table->string('hijriah')->nullable(); // contoh: 1447 H
        $table->boolean('is_aktif')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_kurban');
    }
};
