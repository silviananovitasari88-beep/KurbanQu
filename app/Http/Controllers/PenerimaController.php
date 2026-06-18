<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}

$penerima = DB::table('warga')
    ->leftJoin('distribusi', 'warga.no_kk', '=', 'distribusi.warga_no_kk')
    ->select(
        'warga.*',
        'distribusi.st_pengambilan',
        'distribusi.login',
        'distribusi.id_stok'
    )
    ->get();