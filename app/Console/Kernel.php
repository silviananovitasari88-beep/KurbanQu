<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendWhatsAppPaymentReminder;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan payment reminder check setiap hari jam 08:00 (pagi)
        $schedule->job(new SendWhatsAppPaymentReminder)
            ->dailyAt('08:00')
            ->name('whatsapp-payment-reminder')
            ->description('Send WhatsApp reminders for bookings due in 3 days');
        
        // Alternative: Run every 6 hours untuk testing
        // $schedule->job(new SendWhatsAppPaymentReminder)->everyHour();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
