<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hewan extends Model
{
    protected $table = 'hewan';
    protected $primaryKey = 'id_hewan';

    protected $fillable = [
        'jenis', 'sehat', 'cacat', 'umur', 
        'st_syariat', 'admin_id_admin', 'tracking_id_tracking'
    ];

    public function admin(): BelongsTo {
        return $this->belongsTo(Admin::class, 'admin_id_admin');
    }

    public function tracking(): BelongsTo {
        return $this->belongsTo(Tracking::class, 'tracking_id_tracking');
    }

    public function mudhohi(): HasMany {
        return $this->hasMany(Mudhohi::class, 'hewan_id_hewan');
    }
}