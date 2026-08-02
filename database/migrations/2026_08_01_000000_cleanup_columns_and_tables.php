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
        // 1. Hapus kolom last_login_at & is_online dari tabel warga
        if (Schema::hasTable('warga')) {
            Schema::table('warga', function (Blueprint $table) {
                if (Schema::hasColumn('warga', 'last_login_at')) {
                    $table->dropColumn('last_login_at');
                }
                if (Schema::hasColumn('warga', 'is_online')) {
                    $table->dropColumn('is_online');
                }
            });
        }

        // 2. Hapus kolom email & remember_token dari tabel users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'email')) {
                    $table->dropColumn('email');
                }
                if (Schema::hasColumn('users', 'remember_token')) {
                    $table->dropColumn('remember_token');
                }
            });
        }

        // 3. Hapus kolom tracking_id_tracking dari tabel hewan
        if (Schema::hasTable('hewan')) {
            Schema::table('hewan', function (Blueprint $table) {
                if (Schema::hasColumn('hewan', 'tracking_id_tracking')) {
                    $table->dropColumn('tracking_id_tracking');
                }
            });
        }

        // 4. Hapus tabel tracking & warga_uploads jika ada
        Schema::dropIfExists('tracking');
        Schema::dropIfExists('warga_uploads');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('warga')) {
            Schema::table('warga', function (Blueprint $table) {
                $table->timestamp('last_login_at')->nullable();
                $table->boolean('is_online')->default(false);
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->nullable();
                $table->rememberToken();
            });
        }

        if (Schema::hasTable('hewan')) {
            Schema::table('hewan', function (Blueprint $table) {
                $table->unsignedInteger('tracking_id_tracking')->nullable();
            });
        }
    }
};
