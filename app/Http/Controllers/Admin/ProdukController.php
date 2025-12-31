<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProdukController extends Controller
{
    /**
     * Menampilkan halaman Kelola Menu.
     */
    public function index()
    {
        $produks = Produk::all()->groupBy('category');

        $kategoriProduk = [
            'makanan' => $produks->get('makanan', collect()),
            'minuman' => $produks->get('minuman', collect()),
            'paket'   => $produks->get('paket', collect()),
        ];

        return view('admin.menu.index', compact('kategoriProduk'));
    }

    /**
     * Halaman tambah produk.
     */
    public function create()
    {
        return view('admin.menu.create');
    }

    /**
     * Menyimpan produk baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'price'    => 'required|integer|min:0',
            'category' => 'required|in:makanan,minuman,paket',
            'image'    => 'required|image|mimes:jpeg,jpg,png,webp|max:2048'
        ]);

        // Pastikan folder ada
        $directory = public_path('images/menu');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true);
        }

        // Upload gambar
        $file = $request->file('image');
        $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $file->move($directory, $filename);

        Produk::create([
            'name'      => $request->name,
            'price'     => $request->price,
            'category'  => $request->category,
            'image_url' => 'images/menu/' . $filename
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu baru berhasil ditambahkan!');
    }

    /**
     * Halaman edit.
     */
    public function edit(Produk $produk)
    {
        return view('admin.menu.edit', compact('produk'));
    }

    /**
     * Update produk.
     */
    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'price'    => 'required|integer|min:0',
            'category' => 'required|in:makanan,minuman,paket',
            'image'    => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048'
        ]);

        $data = $request->only(['name', 'price', 'category']);

        // Jika ada gambar baru
        if ($request->hasFile('image')) {

            // Hapus gambar lama jika ada
            if (!empty($produk->image_url)) {
                $oldPath = public_path($produk->image_url);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // Upload gambar baru
            $directory = public_path('images/menu');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true);
            }

            $file = $request->file('image');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move($directory, $filename);

            // Simpan path baru
            $data['image_url'] = 'images/menu/' . $filename;
        }

        $produk->update($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Hapus produk.
     */
    public function destroy(Produk $produk)
    {
        // Hapus gambar jika ada
        if (!empty($produk->image_url)) {
            $path = public_path($produk->image_url);
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        // Hapus dari database
        $produk->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus!');
    }
}
