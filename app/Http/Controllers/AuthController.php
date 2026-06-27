<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        // Jika sudah login, langsung redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login admin.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return response()->json([
                'message' => 'Username atau password salah.',
            ], 401);
        }

        // Regenerasi session untuk mencegah session fixation
        $request->session()->regenerate();

        return response()->json([
            'message'  => 'Login berhasil.',
            'redirect' => route('admin.dashboard'),
        ]);
    }

    /**
     * Proses registrasi akun baru.
     */
    public function register(Request $request)
{
    try {
        $request->validate([
            'username'              => 'required|string|max:50|unique:users,username',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $user = User::create([
            'name'     => $request->username,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
        ]);

        return response()->json(['message' => 'Akun berhasil dibuat.'], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage(), // ← tampilkan error asli
        ], 500);
    }
}
    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }
}