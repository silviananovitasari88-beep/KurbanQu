<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distribusi extends Model
{
    protected $table = 'distribusi';
    protected $primaryKey = 'id_stok';

    protected $fillable = [
    'login',           
    'warga_no_kk', 
    'QR_id_qr', 
    'st_pengambilan', 
    'mtd_pengambilan'
];
    public function warga(): BelongsTo {
        return $this->belongsTo(Warga::class, 'warga_no_kk');
    }

    public function qr(): BelongsTo {
        return $this->belongsTo(QR::class, 'QR_id_qr');
    }

    public function tracking(): HasMany {
        return $this->hasMany(Tracking::class, 'distribusi_id_stok');
    }
}