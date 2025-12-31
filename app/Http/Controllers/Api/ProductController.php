<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        $query = Produk::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();

        // HAPUS SEMUA KODE $products->transform(...) YANG BASE64 KEMARIN.
        // Biarkan Laravel mengirim data apa adanya.
        
        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }
}