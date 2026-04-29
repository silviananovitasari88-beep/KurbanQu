<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Kos;
use Illuminate\Support\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        // Get existing users and rooms
        $users = User::all();
        $rooms = Kos::all();

        if ($users->isEmpty() || $rooms->isEmpty()) {
            $this->command->error('Please create users and rooms first!');
            return;
        }

        // Create sample bookings with different deadline statuses
        $today = Carbon::now();

        // Booking 1: NORMAL (5 days remaining)
        Booking::create([
            'user_id' => $users->first()->id,
            'kos_id' => $rooms->first()->id,
            'approval_status' => 'disetujui',
            'payment_status' => 'unpaid',
            'registration_date' => $today->copy()->subDays(10),
            'payment_deadline' => $today->copy()->addDays(5),
            'harga' => 500000,
            'notes' => 'Normal - Still have time to pay',
        ]);

        // Booking 2: URGENT (2 days remaining)
        if ($rooms->count() > 1) {
            Booking::create([
                'user_id' => $users->count() > 1 ? $users[1]->id : $users->first()->id,
                'kos_id' => $rooms[1]->id,
                'approval_status' => 'disetujui',
                'payment_status' => 'unpaid',
                'registration_date' => $today->copy()->subDays(8),
                'payment_deadline' => $today->copy()->addDays(2),
                'harga' => 500000,
                'notes' => 'URGENT - Payment due in 2 days!',
            ]);
        }

        // Booking 3: OVERDUE (Already past deadline)
        if ($rooms->count() > 2) {
            Booking::create([
                'user_id' => $users->count() > 2 ? $users[2]->id : $users->first()->id,
                'kos_id' => $rooms[2]->id,
                'approval_status' => 'disetujui',
                'payment_status' => 'unpaid',
                'registration_date' => $today->copy()->subDays(35),
                'payment_deadline' => $today->copy()->subDays(3),
                'harga' => 500000,
                'notes' => 'OVERDUE - Payment deadline passed 3 days ago!',
            ]);
        }

        // Booking 4: Already PAID
        if ($rooms->count() > 3) {
            Booking::create([
                'user_id' => $users->count() > 3 ? $users[3]->id : $users->first()->id,
                'kos_id' => $rooms[3]->id,
                'approval_status' => 'disetujui',
                'payment_status' => 'paid',
                'registration_date' => $today->copy()->subDays(5),
                'payment_deadline' => $today->copy()->addDays(25),
                'harga' => 500000,
                'notes' => 'Already paid - next payment due in 25 days',
            ]);
        }

        $this->command->info('✅ Booking seeder completed successfully!');
    }
}
