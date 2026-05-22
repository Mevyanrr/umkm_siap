<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist; // Pastikan model Wishlist di-import
use Illuminate\Support\Facades\Auth;

class BuyerController extends Controller
{
    public function dashboard(Request $request)
    {
        // 1. Ambil semua produk untuk ditampilkan
        $query = Product::with('user');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        $products = $query->latest()->get();

        // 2. AMBIL DATA LOVE USER (Ini yang bikin warna merahnya permanen saat login/refresh!)
        $wishlistIds = [];
        if (Auth::check()) {
            $wishlistIds = Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray(); // Menghasilkan array berisi ['uuid-1', 'uuid-2']
        }

        // 3. Kirim kedua data ke view dashboard.buyer
        return view('dashboard.buyer', compact('products', 'wishlistIds'));
    }
}
