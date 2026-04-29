<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Kos;
use App\Models\Booking;

try {
    // Cek kamar ada
    $kos = Kos::first();
    echo "✓ Kamar ditemukan: Kamar " . $kos->number . "\n";
    
    // Buat user test
    $user = User::firstOrCreate(
        ['email' => 'test456@gmail.com'],
        ['name' => 'Test Booking User', 'no_hp' => '081223288620', 'password' => bcrypt('test')]
    );
    echo "✓ User ditemukan/dibuat: " . $user->name . "\n";
    
    // Buat booking
    $booking = Booking::create([
        'user_id' => $user->id,
        'kos_id' => $kos->id,
        'approval_status' => 'menunggu',
        'payment_status' => 'unpaid',
        'registration_date' => now()->toDateString(),
        'payment_deadline' => now()->addMonth()->toDateString(),
        'harga' => $kos->harga,
        'notes' => 'Test booking dari PHP script'
    ]);
    
    echo "\n✅ BOOKING BERHASIL DIBUAT!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Booking ID: #BK" . $booking->id . "\n";
    echo "Nama: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "No HP: " . $user->no_hp . "\n";
    echo "Kamar: " . $kos->number . "\n";
    echo "Harga: Rp " . number_format($kos->harga, 0, ',', '.') . "\n";
    echo "Status: " . $booking->approval_status . "\n";
    
    // Test WhatsApp URL generation
    $waNumber = '6281223288620';
    $message = urlencode(
        "Halo admin, saya ingin booking kamar.\n\n" .
        "Nama: {$user->name}\n" .
        "No HP: {$user->no_hp}\n" .
        "Kamar: {$kos->number}\n" .
        "Harga: {$kos->harga}\n\n" .
        "Mohon konfirmasinya ya 🙏"
    );
    $waLink = "https://wa.me/{$waNumber}?text={$message}";
    
    echo "\n✓ WhatsApp URL generated:\n";
    echo "   " . substr($waLink, 0, 80) . "...\n";
    
    // Test JSON response
    $jsonResponse = [
        'success' => true,
        'message' => 'Booking berhasil disimpan',
        'booking' => [
            'id' => $booking->id,
            'user_id' => $booking->user_id,
            'kos_id' => $booking->kos_id,
        ],
        'whatsapp_url' => $waLink
    ];
    
    echo "\n✓ JSON Response Structure:\n";
    echo json_encode($jsonResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
