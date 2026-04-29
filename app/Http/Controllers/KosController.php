<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use Illuminate\Http\Request;

class KosController extends Controller
{
    /**
     * Store - Tambah kamar baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|unique:kos,number',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,ditempati',
            'penyewa' => 'nullable|string',
            'foto' => 'nullable|string',
        ]);

        Kos::create($validated);

        return redirect()->route('admin.dashboard', ['section' => 'manage-rooms'])
            ->with('success', 'Kamar berhasil ditambahkan');
    }

    /**
     * Update - Edit kamar
     */
    public function update(Request $request, Kos $kos)
    {
        $validated = $request->validate([
            'number' => 'required|string|unique:kos,number,' . $kos->id,
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,ditempati',
            'penyewa' => 'nullable|string',
            'foto' => 'nullable|string',
        ]);

        $kos->update($validated);

        return redirect()->route('admin.dashboard', ['section' => 'manage-rooms'])
            ->with('success', 'Kamar berhasil diperbarui');
    }

    /**
     * Destroy - Hapus kamar
     */
    public function destroy(Kos $kos)
    {
        \Log::info("Attempting to delete room: {$kos->id} - {$kos->number}");
        
        // Check if there are any bookings for this room
        $bookingCount = $kos->bookings()->count();
        \Log::info("Room {$kos->id} has {$bookingCount} bookings");
        
        if ($bookingCount > 0) {
            \Log::warning("Cannot delete room {$kos->id} - has {$bookingCount} active bookings");
            return redirect()->route('admin.dashboard', ['section' => 'manage-rooms'])
                ->with('error', 'Tidak dapat menghapus kamar karena masih ada booking. Hapus booking terlebih dahulu.');
        }

        try {
            $kos->delete();
            \Log::info("Successfully deleted room {$kos->id}");
            return redirect()->route('admin.dashboard', ['section' => 'manage-rooms'])
                ->with('success', 'Kamar berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error("Error deleting room {$kos->id}: {$e->getMessage()}");
            return redirect()->route('admin.dashboard', ['section' => 'manage-rooms'])
                ->with('error', 'Gagal menghapus kamar: ' . $e->getMessage());
        }
    }
}