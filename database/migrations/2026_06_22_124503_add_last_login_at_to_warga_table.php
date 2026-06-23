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
        Schema::table('warga', function (Blueprint $table) {
            if (!Schema::hasColumn('warga', 'last_login_at')) {
            $table->timestamp('last_login_at')->nullable();
        }
        if (!Schema::hasColumn('warga', 'is_online')) {
            $table->boolean('is_online')->default(false);
        }
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warga', function (Blueprint $table) {
            $table->dropColumn(['last_login_at', 'is_online']);
            //
        });
    }
};
