<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Kasir;
use App\Models\PasswordResetOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Menampilkan halaman form "lupa password" (Form Email)
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Menerima email, mencari user, dan mengarahkan ke halaman OTP.
     */
    public function sendResetLink(Request $request)
    {
        // 1. Validasi email
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        // 2. Cari email di 3 tabel
        $user = User::where('email', $email)->first() 
             ?? Admin::where('email', $email)->first() 
             ?? Kasir::where('email', $email)->first();

        // 3. Jika tidak ditemukan sama sekali
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.']);
        }
        
        // 4. Ambil guard-nya
        $guard = 'web';
        if (Admin::where('email', $email)->exists()) $guard = 'admin';
        elseif (Kasir::where('email', $email)->exists()) $guard = 'kasir';

        // 5. Panggil logika inti pengiriman OTP
        if (!$this->_sendOtpLogic($email)) {
            return back()->withErrors(['email' => 'Gagal mengirim email OTP. Coba lagi nanti.']);
        }

        // 6. Simpan email & guard ke session
        $request->session()->put('password_reset_email', $email);
        $request->session()->put('password_reset_guard', $guard); 

        // 7. Arahkan ke halaman input OTP
        return redirect()->route('password.otp.form');
    }

    /**
     * (BARU) Mengirim ulang OTP
     */
    public function resendOtp(Request $request)
    {
        // 1. Ambil email dari session (BUKAN BIKIN FAKE REQUEST)
        $email = $request->session()->get('password_reset_email');
        
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi Anda telah habis, silakan masukkan email lagi.']);
        }
        
        // 2. Panggil logika inti pengiriman OTP
        if (!$this->_sendOtpLogic($email)) {
            // Jika gagal, kembali ke halaman OTP dengan pesan error
            return redirect()->route('password.otp.form')->withErrors(['otp' => 'Gagal mengirim ulang OTP. Coba lagi nanti.']);
        }

        // 3. Arahkan kembali ke halaman OTP dengan pesan sukses
        return redirect()->route('password.otp.form')->with('status', 'Kode OTP baru telah berhasil dikirim.');
    }


    /**
     * Menampilkan halaman input OTP
     */
    public function showOtpForm()
    {
        if (!session()->has('password_reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Silakan masukkan email Anda terlebih dahulu.']);
        }
        return view('auth.verify-otp');
    }

    /**
     * Memverifikasi OTP dari database
     */
    public function verifyOtp(Request $request)
    {
        // 1. Validasi input
        $request->validate(['otp' => 'required|digits:6']);
        $email = $request->session()->get('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi Anda telah habis, silakan masukkan email lagi.']);
        }

        // 2. Cari OTP di database
        $otpRecord = PasswordResetOtp::where('email', $email)->first();

        // 3. Cek jika OTP-nya ada, belum kadaluarsa, dan cocok
        if (
            $otpRecord &&
            Hash::check($request->otp, $otpRecord->otp) &&
            $otpRecord->expires_at > Carbon::now()
        ) {
            $request->session()->put('password_otp_verified', true);
            $otpRecord->delete();
            return redirect()->route('password.reset.form');
        }

        // 4. Jika salah atau kadaluarsa
        return back()->withErrors(['otp' => 'Kode OTP salah atau telah kadaluarsa.']);
    }

    
    // ... (Fungsi showResetForm, updatePassword, dan showSuccessPage TIDAK BERUBAH) ...
    // ... (Saya sertakan di bawah ini untuk kelengkapan) ...
    
    /**
     * Menampilkan form ganti password baru
     */
    public function showResetForm(Request $request)
    {
        if (!$request->session()->get('password_otp_verified')) {
            return redirect()->route('password.otp.form')->withErrors(['otp' => 'Silakan verifikasi OTP Anda terlebih dahulu.']);
        }
        return view('auth.reset-password');
    }

    /**
     * Memperbarui password di database
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = $request->session()->get('password_reset_email');
        $guard = $request->session()->get('password_reset_guard');
        $otp_verified = $request->session()->get('password_otp_verified');

        if (!$email || !$guard || !$otp_verified) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi Anda telah habis, silakan coba lagi.']);
        }

        $user = null;
        if ($guard == 'web') $user = User::where('email', $email)->first();
        if ($guard == 'admin') $user = Admin::where('email', $email)->first();
        if ($guard == 'kasir') $user = Kasir::where('email', $email)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        } else {
            return redirect()->route('login')->withErrors(['email' => 'Gagal menemukan akun, silakan coba lagi.']);
        }

        $request->session()->forget(['password_reset_email', 'password_reset_guard', 'password_otp_verified']);
        
        return redirect()->route('password.success');
    }

    /**
     * Menampilkan halaman sukses
     */
    public function showSuccessPage()
    {
        return view('auth.reset-success');
    }


    // --- ▼▼▼ FUNGSI BANTU BARU (PRIVATE) ▼▼▼ ---
    /**
     * Logika inti untuk mengirim OTP (dipakai oleh sendResetLink dan resendOtp)
     */
    private function _sendOtpLogic($email)
    {
        // 1. Buat 6 digit OTP
        $otp = rand(100000, 999999);

        // 2. Simpan OTP ke database
        PasswordResetOtp::updateOrCreate(
            ['email' => $email],
            [
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(1),
                'created_at' => Carbon::now()
            ]
        );

        // 3. Kirim email berisi OTP
        try {
            Mail::to($email)->send(new SendOtpMail($otp));
            return true; // Sukses
        } catch (\Exception $e) {
            // (Opsional: Log errornya)
            // \Log::error("Gagal kirim OTP: " . $e->getMessage());
            return false; // Gagal
        }
    }
    // --- ▲▲▲ AKHIR FUNGSI BANTU ▲▲▲ ---
}