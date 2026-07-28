<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    /**
     * Ambil data tracking steps
     */
    public function getSteps()
    {
        $steps = DB::table('tracking_steps')->orderBy('urutan')->get();

        if ($steps->isEmpty()) {
            $default = [
                ['urutan'=>1,'label'=>'Penyembelihan','status'=>'pending','time'=>null],
                ['urutan'=>2,'label'=>'Pengulitan',   'status'=>'pending','time'=>null],
                ['urutan'=>3,'label'=>'Pencacahan',   'status'=>'pending','time'=>null],
                ['urutan'=>4,'label'=>'Penimbangan',  'status'=>'pending','time'=>null],
                ['urutan'=>5,'label'=>'Siap Diambil', 'status'=>'pending','time'=>null],
            ];
            return response()->json([
                'success' => true,
                'steps' => array_map(fn($s) => [
                    'status' => $s['status'],
                    'time'   => $s['time'] ?? '—',
                ], $default),
            ]);
        }

        return response()->json([
            'success' => true,
            'steps' => $steps->map(fn($s) => [
                'status' => $s->status,
                'time'   => $s->time ?? '—',
            ])->values(),
        ]);
    }

    /**
     * Perbarui status tahap tertentu
     */
    public function updateStep(Request $request, $urutan)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,active,done',
        ]);

        $exists = DB::table('tracking_steps')->where('urutan', $urutan)->exists();
        $labels = ['Penyembelihan','Pengulitan','Pencacahan','Penimbangan','Siap Diambil'];
        $now = now()->format('H:i') . ' WIB';

        if ($exists) {
            DB::table('tracking_steps')->where('urutan', $urutan)->update([
                'status' => $data['status'],
                'time'   => $data['status'] !== 'pending' ? $now : null,
            ]);
        } else {
            DB::table('tracking_steps')->insert([
                'urutan' => $urutan,
                'label'  => $labels[$urutan - 1] ?? "Tahap $urutan",
                'status' => $data['status'],
                'time'   => $data['status'] !== 'pending' ? $now : null,
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Reset semua tahap ke pending
     */
    public function reset()
    {
        DB::table('tracking_steps')->update(['status' => 'pending', 'time' => null]);
        return response()->json(['success' => true]);
    }
}