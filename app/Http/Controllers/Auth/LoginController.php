<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Method untuk menampilkan form login staff
    public function showStaffLoginForm()
    {
        return view('auth.login_staff');
    }

    // Method untuk memproses login staff
    public function staffLogin(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginInput = $request->input('email');
        $passwordInput = $request->input('password');

        // 2. Tentukan nama kolom (email atau name)
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'name';

        $credentials = [
            $field => $loginInput,
            'password' => $passwordInput
        ];
        
        $guards = ['admin', 'kasir', 'koki'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->attempt($credentials)) {
                $request->session()->regenerate();

                // Arahkan ke dashboard yang sesuai
                switch ($guard) {
                    case 'admin':
                        return redirect()->intended(route('admin.laporan.index'));
                    case 'kasir':
                        return redirect()->intended(route('kasir.dashboard'));
                    case 'koki':
                        return redirect()->intended(route('koki.dashboard'));
                }
            }
        }

        // Jika semua gagal
        return back()->withErrors([
            'email' => 'Username/Email atau password salah.',
        ])->onlyInput('email');
    }

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
        
        // Coba login sebagai User (pelanggan)
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('user.dashboard'));
        }

        // Jika gagal
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