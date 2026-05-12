<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QR extends Model
{
    protected $table = 'QR';
    protected $primaryKey = 'id_qr';

    protected $fillable = [
        'no_antrian', 'dur_sesi', 'loc_pengambilan', 'jam_pengambilan'
    ];

    public function distribusi(): HasMany {
        return $this->hasMany(Distribusi::class, 'QR_id_qr');
    }
}