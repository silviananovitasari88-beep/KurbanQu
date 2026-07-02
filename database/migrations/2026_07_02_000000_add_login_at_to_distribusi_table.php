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
        if (!Schema::hasTable('distribusi')) {
            return;
        }

        Schema::table('distribusi', function (Blueprint $table) {
            if (!Schema::hasColumn('distribusi', 'status_login')) {
                $table->string('status_login', 20)->default('Belum Login')->after('login');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('distribusi')) {
            return;
        }

        Schema::table('distribusi', function (Blueprint $table) {
            if (Schema::hasColumn('distribusi', 'status_login')) {
                $table->dropColumn('status_login');
            }
        });
    }
};
