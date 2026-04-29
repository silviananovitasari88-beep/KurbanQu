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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kos_id')->constrained('kos')->onDelete('cascade');
            
            // Status untuk approval
            $table->enum('approval_status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            
            // Status untuk pembayaran
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            
            // Tanggal dan harga
            $table->date('registration_date');
            $table->date('payment_deadline');
            $table->decimal('harga', 10, 2);
            
            // Catatan dan tracking
            $table->text('notes')->nullable();
            $table->timestamp('reminded_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
