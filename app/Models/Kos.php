<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kos extends Model
{
    use HasFactory;
    // Nama tabel di database (opsional jika nama tabelnya 'kos')
    protected $table = 'kos';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = ['number', 'harga', 'status', 'penyewa', 'foto'];
    
    /**
     * Relasi: Satu Kos bisa memiliki banyak Booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
