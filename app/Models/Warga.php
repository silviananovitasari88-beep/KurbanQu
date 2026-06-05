<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warga extends Model
{
    protected $table = 'warga';
    protected $primaryKey = 'no_kk';
    public $incrementing = false; // Karena no_kk biasanya bukan auto-increment
    public $timestamps = false; // ← Disable auto timestamps, kita handle manual

    protected $fillable = [
        'no_kk', 'nama_kk', 'alamat', 'no_telp', 'QR_id_qr', 'id_penerima', 'created_at', 'updated_at'
    ];

    public function distribusi(): HasMany {
        return $this->hasMany(Distribusi::class, 'warga_no_kk');
    }
}