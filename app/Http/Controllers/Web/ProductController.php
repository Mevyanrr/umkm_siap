<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('umkm.produk_saya', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'category'            => 'nullable|string|max:50',
            'description'         => 'nullable|string|max:2000',
            'production_capacity' => 'nullable|string|max:50',
            'target_countries'    => 'nullable|string|max:255',
            'certifications'      => 'nullable|string|max:255',
            'images'              => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $imageUrl = null;
        if ($request->hasFile('images')) {
            $path = $request->file('images')->store('products/' . Auth::id(), 'public');
            $imageUrl = Storage::url($path);
        }

        Product::create([
            'user_id'             => Auth::id(),
            'name'                => $request->name,
            'category'            => $request->category,
            'description'         => $request->description,
            'price_usd'           => $request->price_usd ?? 0,
            'production_capacity' => $request->production_capacity,
            'target_countries'    => $request->target_countries ? explode(',', $request->target_countries) : [],
            'certifications'      => $request->certifications ? explode(',', $request->certifications) : [],
            'images'              => $imageUrl ? [$imageUrl] : [],
            'status'              => 'published',
        ]);

        return redirect()->route('umkm.produk_saya')->with('success', 'Produk berhasil diupload!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'name'                => 'required|string|max:255',
            'category'            => 'nullable|string|max:50',
            'description'         => 'nullable|string|max:2000',
            'production_capacity' => 'nullable|string|max:50',
            'target_countries'    => 'nullable|string|max:255',
            'certifications'      => 'nullable|string|max:255',
            'images'              => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $imageUrl = $product->images[0] ?? null;
        if ($request->hasFile('images')) {
            $path = $request->file('images')->store('products/' . Auth::id(), 'public');
            $imageUrl = Storage::url($path);
        }

        $product->update([
            'name'                => $request->name,
            'category'            => $request->category,
            'description'         => $request->description,
            'price_usd'           => $request->price_usd ?? $product->price_usd,
            'production_capacity' => $request->production_capacity,
            'target_countries'    => $request->target_countries ? explode(',', $request->target_countries) : [],
            'certifications'      => $request->certifications ? explode(',', $request->certifications) : [],
            'images'              => $imageUrl ? [$imageUrl] : [],
        ]);

        return redirect()->route('umkm.produk_saya')->with('success', 'Produk berhasil diupdate!');
    }

    public function destroy($id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $product->delete();

        return redirect()->route('umkm.produk_saya')->with('success', 'Produk berhasil dihapus!');
    }
}