<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tracking extends Model
{
    protected $table = 'tracking';
    protected $primaryKey = 'id_tracking';

    protected $fillable = [
        'st_tracking', 'time_tracking', 'distribusi_id_stok'
    ];

    public function hewan(): HasMany {
        return $this->hasMany(Hewan::class, 'tracking_id_tracking');
    }

    public function distribusi(): BelongsTo {
        return $this->belongsTo(Distribusi::class, 'distribusi_id_stok');
    }
}