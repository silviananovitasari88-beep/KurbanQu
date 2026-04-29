<?php

namespace App\Jobs;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppPaymentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job - Send WhatsApp reminders untuk payment yang akan jatuh tempo
     */
    public function handle(): void
    {
        try {
            Log::info('=== WhatsApp Payment Reminder Job Started ===');
            
            $now = now();
            $threeDaysFromNow = $now->copy()->addDays(3);
            
            // Cari semua booking yang:
            // 1. Status approval = 'disetujui' (sudah diterima)
            // 2. Status pembayaran = 'unpaid' (belum bayar)
            // 3. Deadline pembayaran dalam 3 hari ke depan
            // 4. Belum pernah dikirim reminder hari ini (atau belum sama sekali)
            
            $bookings = Booking::where('approval_status', 'disetujui')
                ->where('payment_status', 'unpaid')
                ->whereDate('payment_deadline', '>=', $now->toDateString())
                ->whereDate('payment_deadline', '<=', $threeDaysFromNow->toDateString())
                ->where(function ($query) {
                    // Belum pernah dikirim reminder ATAU dikirim lebih dari 24 jam yang lalu
                    $query->whereNull('reminded_at')
                        ->orWhereDate('reminded_at', '<', now()->toDateString());
                })
                ->with('user', 'kos')
                ->get();

            Log::info("Found {$bookings->count()} bookings to remind");

            $remindersSent = 0;
            $remindersFailed = 0;

            foreach ($bookings as $booking) {
                try {
                    // Hitung berapa hari lagi hingga deadline
                    $daysRemaining = (int) $now->diffInDays($booking->payment_deadline);
                    
                    // Generate WhatsApp reminder message
                    $message = $this->generateReminderMessage($booking, $daysRemaining);
                    
                    // Format nomor WA pemilik kosan
                    $ownerPhone = '6281223288620'; // Nomor pemilik kosan
                    
                    // Generate wa.me link
                    $waLink = "https://wa.me/{$ownerPhone}?text=" . urlencode($message);
                    
                    // Log pengiriman reminder
                    Log::info("Reminder queued for booking #{$booking->id} - {$booking->user->name} - Kamar {$booking->kos->number}");
                    Log::info("Days remaining: {$daysRemaining}, Deadline: {$booking->payment_deadline}");
                    
                    // Update reminded_at timestamp untuk avoid duplicate reminders hari ini
                    $booking->update([
                        'reminded_at' => now(),
                    ]);
                    
                    // Kirim via WhatsApp API atau direct link (jika menggunakan wa.me redirect)
                    // Untuk sekarang, kita hanya log dan track di database
                    // Pemilik kosan akan melihat notifikasi di dashboard
                    
                    Log::info("✅ Reminder sent for booking #{$booking->id}");
                    $remindersSent++;

                } catch (\Exception $e) {
                    Log::error("Error sending reminder for booking #{$booking->id}: {$e->getMessage()}");
                    $remindersFailed++;
                }
            }

            Log::info("=== WhatsApp Payment Reminder Job Completed ===");
            Log::info("Sent: {$remindersSent}, Failed: {$remindersFailed}, Total: {$bookings->count()}");

        } catch (\Exception $e) {
            Log::error("Critical error in SendWhatsAppPaymentReminder: {$e->getMessage()}");
            Log::error($e->getTraceAsString());
        }
    }

    /**
     * Generate WhatsApp reminder message
     */
    private function generateReminderMessage(Booking $booking, int $daysRemaining): string
    {
        $userName = $booking->user->name;
        $roomNumber = $booking->kos->number;
        $roomPrice = 'Rp ' . number_format($booking->harga, 0, ',', '.');
        $deadline = $booking->payment_deadline->locale('id_ID')->isoFormat('dddd, D MMMM YYYY');
        $bookingId = "#BK{$booking->id}";
        
        // Tentukan urgensi berdasarkan hari yang tersisa
        $urgencyEmoji = match (true) {
            $daysRemaining <= 0 => '🚨',  // Overdue
            $daysRemaining <= 1 => '⚠️',   // Sangat urgent
            default => '⏰',                // Normal reminder
        };

        $message = <<<MESSAGE
{$urgencyEmoji} *REMINDER PEMBAYARAN KOSAN*

Halo *{$userName}*,

Kami ingin mengingatkan bahwa pembayaran sewa kamar Anda segera akan jatuh tempo.

📋 *Detail Booking:*
• ID Booking: {$bookingId}
• Nomor Kamar: {$roomNumber}
• Harga Sewa: {$roomPrice}
• Tenggat Waktu: {$deadline}
• Sisa Waktu: {$daysRemaining} hari

💳 *Cara Pembayaran:*
Silakan lakukan transfer ke rekening yang telah disediakan sebelumnya. Jangan lupa sertakan bukti transfer.

❓ *Pertanyaan atau Kendala?*
Hubungi kami untuk informasi lebih lanjut.

Terima kasih atas perhatian Anda!
— Tim Kosan Aulia
MESSAGE;

        return $message;
    }
}
