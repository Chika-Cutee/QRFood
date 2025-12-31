<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Fungsi register (tetap sama)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => $user
        ], 201);
    }

    // ==========================================================
    // --- FUNGSI LOGIN YANG SUDAH DIPERBARUI ---
    // ==========================================================
    public function login(Request $request)
    {
        // 1. Validasi input baru (kita sebut 'login' bukan 'email')
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->login;
        $password = $request->password;

        // 2. Tentukan apakah input adalah email atau nama (username)
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // 3. Coba login menggunakan email atau nama
        $credentials = [
            $field => $loginInput,
            'password' => $password
        ];

        if (!Auth::attempt($credentials)) {
             return response()->json([
                'success' => false,
                'message' => 'Username/Email atau password salah.'
            ], 401);
        }

        // 4. Jika berhasil, ambil data user
        $user = User::where($field, $loginInput)->firstOrFail();
        
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => $user,
        ], 200);
    }
}