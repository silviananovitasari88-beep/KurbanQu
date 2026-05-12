<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mudhohi extends Model
{
    protected $table = 'mudhohi';
    protected $primaryKey = 'id_mudhohi';

    protected $fillable = [
        'nama_mudhohi', 'nama_ayah', 'alamat', 
        'notelp_mudhohi', 'req_bagian', 'admin_id_admin', 'hewan_id_hewan'
    ];

    public function admin(): BelongsTo {
        return $this->belongsTo(Admin::class, 'admin_id_admin');
    }

    public function hewan(): BelongsTo {
        return $this->belongsTo(Hewan::class, 'hewan_id_hewan');
    }
}