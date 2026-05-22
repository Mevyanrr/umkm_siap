<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class WhistlistController extends Controller
{
    /**
     * TOGGLE LOVE / WISHLIST (AJAX)
     */
    public function toggle(Request $request)
    {
        // Validasi input pastikan ID produk ada di DB
        $request->validate([
            'product_id' => 'required|uuid|exists:products,id',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        // Cari apakah user sudah pernah nge-love produk ini
        $wishlist = Wishlist::where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->first();

        if ($wishlist) {
            // Jika sudah ada, hapus (Unlike / Un-wishlist)
            $wishlist->delete();

            return response()->json([
                'status' => 'success',
                'action' => 'removed',
                'message' => 'Produk dihapus dari wishlist.'
            ]);
        } else {
            // Jauh lebih aman menggunakan baris baru manual untuk menghindari proteksiyon Mass Assignment $fillable
            $newWishlist = new Wishlist();
            $newWishlist->user_id = $userId;
            $newWishlist->product_id = $productId;
            $newWishlist->save();

            return response()->json([
                'status' => 'success',
                'action' => 'added',
                'message' => 'Produk berhasil ditambahkan ke wishlist.'
            ]);
        }
    }

    /**
     * HALAMAN PRODUK TERSIMPAN (DENGAN DESAIN LIST TABEL)
     */
   public function savedProducts()
{
    $userId = Auth::id();

    // Ambil data produk yang di-wishlist oleh user saat ini
    $savedProducts = Product::whereIn('id', function($query) use ($userId) {
        $query->select('product_id')
              ->from('wishlists')
              ->where('user_id', $userId);
    })->with('user')->latest()->get();

    $totalFavorit = $savedProducts->count();

    // PERBAIKAN BARIS 74: Ubah 'buyer.saved' menjadi 'buyer.save'
    return view('buyer.save', compact('savedProducts', 'totalFavorit'));
}
}
