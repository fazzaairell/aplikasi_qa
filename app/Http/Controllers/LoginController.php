<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login'); // Mengarahkan ke file view login yang kamu buat
    }

    // Memproses data login
    public function login(Request $request)
    {
        // 1. Validasi input dari form frontend
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Cek status "Ingat saya selama 30 hari"
        $remember = $request->has('remember');

        // 3. Proses autentikasi menggunakan Laravel Auth
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = strtolower($user->role);

            // 4. Pengalihan dinamis berdasarkan role
            return match(true) {
                $role === 'admin' => redirect()->intended('/dashboard'),
                in_array($role, ['qa lead', 'qa tester']) => redirect()->intended('/dashboard/qa'),
                $role === 'developer' => redirect()->intended('/dashboard/developer'),
                default => redirect()->intended('/projects'),
            };
        }

        // 5. Jika gagal (email/password salah)
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}