<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordResetOtp; // Model yang kita buat
use App\Mail\SendOtpMail; // Mail class yang kita buat
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    // 1. Menerjemahkan requestOtp.php
    public function requestOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        // Cek jika email terdaftar
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email tidak terdaftar'], 404);
        }

        // Buat OTP
        $otp = rand(100000, 999999);
        $expires_at = Carbon::now()->addMinutes(1);

        // Hapus OTP lama
        PasswordResetOtp::where('email', $request->email)->delete();
        // Simpan OTP baru
        PasswordResetOtp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => $expires_at,
        ]);

        // Kirim email
        try {
            Mail::to($request->email)->send(new SendOtpMail($otp));
            return response()->json(['success' => true, 'message' => 'OTP telah dikirim ke email Anda.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()], 500);
        }
    }

    // 2. Menerjemahkan verifyOtp.php
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $otpData = PasswordResetOtp::where('email', $request->email)
                                ->where('otp', $request->otp)
                                ->where('expires_at', '>', Carbon::now())
                                ->first();

        if ($otpData) {
            return response()->json(['success' => true, 'message' => 'OTP valid.']);
        } else {
            return response()->json(['success' => false, 'message' => 'OTP salah atau telah kadaluarsa.'], 401);
        }
    }

    // 3. Menerjemahkan updatePassword.php
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        // Verifikasi ulang OTP
        $otpData = PasswordResetOtp::where('email', $request->email)
                                ->where('otp', $request->otp)
                                ->where('expires_at', '>', Carbon::now())
                                ->first();

        if (!$otpData) {
            return response()->json(['success' => false, 'message' => 'OTP salah atau telah kadaluarsa.'], 401);
        }

        // Jika OTP valid, update password user
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus OTP setelah berhasil digunakan
        PasswordResetOtp::where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Password berhasil diperbarui.']);
    }
}