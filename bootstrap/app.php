<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\SendWhatsAppPaymentReminder;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\AdminAuthMiddleware::class,
        ]);
        
    })
    ->withSchedule(function (Schedule $schedule) {
        // Send WhatsApp payment reminders daily at 08:00
        $schedule->job(new SendWhatsAppPaymentReminder)
            ->dailyAt('08:00')
            ->name('whatsapp-payment-reminder')
            ->description('Send WhatsApp reminders for bookings due in 3 days');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
