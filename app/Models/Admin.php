<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    
    protected $fillable = [
        'nama_adm', 
        'pw_adm'
    ];

    public function hewan(): HasMany {
        return $this->hasMany(Hewan::class, 'admin_id_admin');
    }

    public function mudhohi(): HasMany {
        return $this->hasMany(Mudhohi::class, 'admin_id_admin');
    }
}