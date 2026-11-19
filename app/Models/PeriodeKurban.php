<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeKurban extends Model
{
    protected $table = 'periode_kurban';

    protected $fillable = ['tahun', 'hijriah', 'is_aktif'];
}