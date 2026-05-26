<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hewan extends Model
{
    protected $table = 'hewan';
    protected $primaryKey = 'id_hewan';

    /** @var list<string> enum jenis hewan */
    public const JENIS = ['sapi', 'kambing', 'domba'];

    protected $fillable = [
        'jenis', 'label', 'sehat', 'cacat', 'cacat_ket', 'umur',
        'st_syariat', 'berat',
        'admin_id_admin', 'tracking_id_tracking',
    ];

    protected $casts = [
        'sehat' => 'string',
        'cacat' => 'string',
        'st_syariat' => 'string',
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