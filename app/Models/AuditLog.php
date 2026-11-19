<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Riwayat perubahan data. Hanya boleh DITAMBAH, tidak boleh diubah atau dihapus.
 * (Pengaman kedua ada di database lewat trigger pada migration audit_log.)
 */
class AuditLog extends Model
{
    protected $table = 'audit_log';
    public $timestamps = false;

    protected $fillable = [
        'aktor', 'aksi', 'tabel', 'data_id', 'periode_id', 'lama', 'baru', 'alasan',
    ];

    protected $casts = [
        'lama' => 'array',
        'baru' => 'array',
    ];

    protected static function booted(): void
    {
        // Membatalkan setiap upaya ubah atau hapus lewat aplikasi
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }
}
