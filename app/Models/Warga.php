<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warga extends Model
{
    protected $table = 'warga';
    protected $primaryKey = 'no_kk';
    public $incrementing = false;
    public $timestamps = true; // ⭐ UBAH KE TRUE
    
    protected $fillable = [
        'username',
        'password',
        'role',
        'no_kk',
        'nama_kk',
        'alamat',
        'no_telp',
        'QR_id_qr',
        'id_penerima',
        'last_login_at',
        'is_online',
        'created_at',
        'updated_at'
    ];

    public function distribusi(): HasMany {
        return $this->hasMany(Distribusi::class, 'warga_no_kk', 'no_kk');
    }
}