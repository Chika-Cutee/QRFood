<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan form login (welcome.blade.php)
    public function showLoginForm()
    {
        return view('welcome');
    }

    // Proses login
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginInput = $request->input('email');
        $passwordInput = $request->input('password');

        // 3. Tentukan nama kolom (email atau name)
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'name';

        $credentials = [
            $field => $loginInput,
            'password' => $passwordInput
        ];
        
        // --- ▼▼▼ PERUBAHAN DI SINI ▼▼▼ ---
        // 1. Coba login sebagai Admin
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            // Ganti dari 'admin.menu.index' menjadi 'admin.laporan.index'
            return redirect()->intended(route('admin.laporan.index'));
        }
        // --- ▲▲▲ AKHIR PERUBAHAN ▲▲▲ ---

        // 2. Coba login sebagai Kasir
        if (Auth::guard('kasir')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('kasir.dashboard'));
        }

        // 3. (BARU) Coba login sebagai Koki
        if (Auth::guard('koki')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('koki.dashboard'));
        }

        // 4. Coba login sebagai User
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('user.dashboard'));
        }

        // Jika semua gagal
        return back()->withErrors([
            'email' => 'Username/Email atau password salah.',
        ])->onlyInput('email');
    }

    // Proses logout
    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif (Auth::guard('kasir')->check()) {
            Auth::guard('kasir')->logout();
        } elseif (Auth::guard('koki')->check()) {
            Auth::guard('koki')->logout();
        } else {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}