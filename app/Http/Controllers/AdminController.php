<?php

namespace App\Http\Controllers;

use App\Models\Kos;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Menampilkan form login admin
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle login admin
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek di database atau gunakan hardcoded fallback
        $admin = User::where('email', $request->username)->first();
        
        if ($admin && Hash::check($request->password, $admin->password)) {
            Session::put('admin_logged_in', true);
            Session::put('admin_id', $admin->id);
            return redirect()->route('admin.dashboard');
        }

        // Fallback ke credentials default jika belum ada di database
        if ($request->username === 'admin' && $request->password === 'admin') {
            Session::put('admin_logged_in', true);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['error' => 'Username atau password salah'])->onlyInput('username');
    }

    /**
     * Menampilkan dashboard admin dengan data dari database
     */
    public function dashboard(Request $request)
    {
        // Check apakah sudah login
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $section = $request->get('section', 'dashboard');
        
        // Get data dari database
        $rooms = Kos::all();
        try {
            $bookings = Booking::with('user', 'kos')->get();
        } catch (\Exception $e) {
            $bookings = collect(); // Return empty collection if table doesn't exist
        }

        // Calculate deadline status untuk setiap booking
        $bookings = $bookings->map(function($booking) {
            $now = now();
            $daysRemaining = (int) $now->diffInDays($booking->payment_deadline);
            
            // Determine deadline status
            if ($now->format('Y-m-d') > $booking->payment_deadline->format('Y-m-d')) {
                $booking->deadline_status = 'overdue';  // Red
                $booking->deadline_badge = '❌ LEWAT (' . abs($daysRemaining) . ' hari)';
            } elseif ($daysRemaining <= 3 && $daysRemaining >= 0) {
                $booking->deadline_status = 'urgent';  // Orange
                $booking->deadline_badge = '⚠️ URGENT (' . $daysRemaining . ' hari)';
            } elseif ($daysRemaining > 3) {
                $booking->deadline_status = 'normal';  // Green
                $booking->deadline_badge = '✅ ' . $daysRemaining . ' hari';
            }
            
            return $booking;
        });

        // Calculate stats
        $totalRooms = $rooms->count();
        $availableRooms = $rooms->where('status', 'tersedia')->count();
        $occupiedRooms = $totalRooms - $availableRooms;

        return view('admin.dashboard', [
            'section' => $section,
            'totalRooms' => $totalRooms,
            'availableRooms' => $availableRooms,
            'occupiedRooms' => $occupiedRooms,
            'rooms' => $rooms,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        Session::forget('admin_logged_in');
        return redirect()->route('admin.login');
    }

    /**
     * Update settings admin
     */
    public function updateSettings(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'old_password' => 'required',
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        // Cek apakah sudah ada admin di database
        $admin = User::first();

        if (!$admin) {
            // Jika belum ada admin, buat user admin baru
            // Verify old password dengan default 'admin'
            if ($request->old_password !== 'admin') {
                return back()->withErrors(['old_password' => 'Password lama salah']);
            }

            $admin = User::create([
                'name' => 'Admin',
                'email' => $request->username,
                'password' => Hash::make($request->password),
                'no_hp' => '-',
            ]);

            return back()->with('success', 'Admin berhasil dibuat dan password diperbarui!');
        }

        // Jika sudah ada, verify old password dari database
        if (!Hash::check($request->old_password, $admin->password)) {
            // Fallback: cek apakah old password adalah 'admin' (default)
            if ($request->old_password !== 'admin') {
                return back()->withErrors(['old_password' => 'Password lama salah']);
            }
        }

        // Update username dan password
        $admin->update([
            'email' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        Session::put('admin_logged_in', true);
        Session::put('admin_id', $admin->id);

        return back()->with('success', 'Username dan password admin berhasil diperbarui!');
    }
}
