<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    // Menentukan kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'user_id', 'kos_id', 'approval_status', 'payment_status',
        'registration_date', 'payment_deadline', 'harga', 'notes', 'reminded_at'
    ];

    protected $casts = [
        'registration_date' => 'date',
        'payment_deadline' => 'date',
        'reminded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User: Booking ini punya siapa?
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Kos: Kamar mana yang di-booking?
     */
    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class);
    }

    /**
     * Helper: Hitung sisa hari sampai payment deadline
     */
    public function daysUntilDeadline()
    {
        return now()->diffInDays($this->payment_deadline, false);
    }

    /**
     * Helper: Check apakah pembayaran sudah jatuh tempo
     */
    public function isOverdue()
    {
        return $this->daysUntilDeadline() < 0;
    }

    /**
     * Helper: Check apakah perlu diingatkan (< 3 hari)
     */
    public function needsReminder()
    {
        $days = $this->daysUntilDeadline();
        return $days <= 3 && $days > 0 && $this->reminded_at === null && $this->payment_status === 'unpaid';
    }
}