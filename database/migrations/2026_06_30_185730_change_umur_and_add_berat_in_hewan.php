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
        if (!Schema::hasTable('hewan')) {
            return;
        }

        Schema::table('hewan', function (Blueprint $table) {
            if (!Schema::hasColumn('hewan', 'umur')) {
                $table->string('umur', 50)->nullable()->after('cacat');
            }

            if (!Schema::hasColumn('hewan', 'berat')) {
                $table->string('berat', 50)->nullable()->after('umur');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('hewan')) {
            return;
        }

        Schema::table('hewan', function (Blueprint $table) {
            if (Schema::hasColumn('hewan', 'berat')) {
                $table->dropColumn('berat');
            }
        });
    }
};
