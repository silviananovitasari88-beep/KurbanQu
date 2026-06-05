<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WargaUpload extends Model
{
    protected $table = 'warga_uploads';
    public $timestamps = false; // Kita handle manually di database

    protected $fillable = [
        'filename', 
        'jumlah_baris', 
        'mode', 
        'admin_id', 
        'status', 
        'error_message', 
        'uploaded_at', 
        'processed_at'
    ];

  
}
