<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kasir; // <-- Import model Kasir
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // <-- Import Hash untuk password
use Illuminate\Validation\Rules;

class KasirController extends Controller
{
    /**
     * Menampilkan halaman daftar kasir. (Gambar 1)
     */
    public function index()
    {
        $kasirs = Kasir::all();
        return view('admin.kasir.index', compact('kasirs'));
    }

    /**
     * Menampilkan form tambah kasir. (Gambar 2)
     */
    public function create()
    {
        return view('admin.kasir.create');
    }

    /**
     * Menyimpan kasir baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.Kasir::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Buat kasir baru
        Kasir::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
        ]);

        // Arahkan kembali ke halaman daftar
        return redirect()->route('admin.kasir.index')->with('success', 'Akun kasir berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit kasir.
     */
    public function edit(Kasir $kasir)
    {
        return view('admin.kasir.edit', compact('kasir'));
    }

    /**
     * Memperbarui data kasir di database.
     */
    public function update(Request $request, Kasir $kasir)
    {
        // Validasi data (email harus unik, tapi abaikan email kasir saat ini)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:kasirs,email,' . $kasir->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Password opsional
        ]);

        // Siapkan data
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Cek jika admin mengisi password baru
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        // Update data
        $kasir->update($data);

        return redirect()->route('admin.kasir.index')->with('success', 'Akun kasir berhasil diperbarui.');
    }

    /**
     * Menghapus akun kasir.
     */
    public function destroy(Kasir $kasir)
    {
        $kasir->delete();
        return redirect()->route('admin.kasir.index')->with('success', 'Akun kasir berhasil dihapus.');
    }
}