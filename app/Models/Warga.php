<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warga extends Model
{
    protected $table = 'warga';
    protected $primaryKey = 'no_kk';
    public $incrementing = false; // Karena no_kk biasanya bukan auto-increment

    protected $fillable = [
        'no_kk', 'nama_kk', 'QR_id_qr'
    ];

    public function distribusi(): HasMany {
        return $this->hasMany(Distribusi::class, 'warga_no_kk');
    }
}