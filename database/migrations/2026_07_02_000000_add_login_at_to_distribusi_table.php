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
        Schema::table('distribusi', function (Blueprint $table) {
            if (!Schema::hasColumn('distribusi', 'login_at')) {
                $table->timestamp('login_at')->nullable()->after('login');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusi', function (Blueprint $table) {
            if (Schema::hasColumn('distribusi', 'login_at')) {
                $table->dropColumn('login_at');
            }
        });
    }
};
