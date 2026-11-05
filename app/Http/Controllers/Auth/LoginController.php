<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /** Guard staff => nama rute dashboard tujuan. */
    private const RUTE_STAFF = [
        'admin' => 'admin.laporan.index',
        'kasir' => 'kasir.dashboard',
        'koki'  => 'koki.dashboard',
    ];

    /**
     * [Duplikasi rutinitas] Pembentukan kredensial (email atau username) sebelumnya
     * ditulis dua kali di staffLogin() dan login().
     */
    private function buatKredensial(Request $request): array
    {
        $loginInput = $request->input('email');
        $field      = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        return [
            $field     => $loginInput,
            'password' => $request->input('password'),
        ];
    }

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

        $credentials = $this->buatKredensial($request);

        // [Pedoman Kontrol] Loop 'for' atas daftar guard yang jumlahnya tetap (3), dan
        // switch lama (tanpa 'default') diganti peta guard -> rute sehingga tidak ada
        // percabangan yang bisa "jatuh" tanpa penanganan.
        foreach (self::RUTE_STAFF as $guard => $namaRute) {
            if (Auth::guard($guard)->attempt($credentials)) {
                $request->session()->regenerate();

                return redirect()->intended(route($namaRute));
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

        $credentials = $this->buatKredensial($request);

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