<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendWhatsAppPaymentReminder;

class TestReminderJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp payment reminder job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching WhatsApp payment reminder job...');
        
        dispatch(new SendWhatsAppPaymentReminder());
        
        $this->info('✅ Job dispatched successfully!');
        $this->info('Check storage/logs/laravel.log for details.');
    }
}
