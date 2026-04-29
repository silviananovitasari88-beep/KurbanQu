<?php

namespace App\Http\Controllers;

use App\Models\Kos;

class KosanController extends Controller
{
    /**
     * Display the kosan homepage dengan data dari database
     */
    public function index()
    {
        // Ambil semua data kamar dari database
        $rooms = Kos::all();

        return view('kosan.home', [
            'rooms' => $rooms
        ]);
    }


}
