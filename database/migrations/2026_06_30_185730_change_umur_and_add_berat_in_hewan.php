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
        Schema::table('hewan', function (Blueprint $table) {
           $table->string('umur', 50)->nullable()->change();
            $table->string('berat', 50)->nullable()->after('umur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hewan', function (Blueprint $table) {
            $table->dropColumn('berat');
            $table->enum('umur', ['Terpenuhi', 'Tidak Terpenuhi'])->nullable()->change();
        });
    }
};
