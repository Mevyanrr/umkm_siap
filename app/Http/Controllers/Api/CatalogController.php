<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    // GET /api/v1/catalog/products
    public function index(Request $request)
    {
        $validated = $request->validate([
            'category'       => 'nullable|string|max:50',
            'target_country' => 'nullable|string|max:2',
            'min_capacity'   => 'nullable|integer|min:1',
            'certified'      => 'nullable|string|max:50',
            'search'         => 'nullable|string|max:100',
            'page'           => 'nullable|integer|min:1',
        ]);

        $query = Product::with('seller:id,name,phone')
            ->where('status', 'published');

        if (!empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }
        if (!empty($validated['target_country'])) {
            $query->whereJsonContains('target_countries', strtoupper($validated['target_country']));
        }
        if (!empty($validated['min_capacity'])) {
            $query->where('production_capacity', '>=', $validated['min_capacity']);
        }
        if (!empty($validated['certified'])) {
            $query->whereJsonContains('certifications', $validated['certified']);
        }
        if (!empty($validated['search'])) {
            $query->where('name', 'ilike', "%{$validated['search']}%");
        }

        $results = $query->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'data'     => $results->items(),
            'total'    => $results->total(),
            'page'     => $results->currentPage(),
            'per_page' => $results->perPage(),
        ]);
    }

    // GET /api/v1/catalog/products/:id
    public function show(string $id)
    {
        $product = Product::with('seller:id,name,phone,email')->findOrFail($id);
        return response()->json($product);
    }

    // POST /api/v1/catalog/products
    public function store(Request $request)
    {
        if (auth('api')->user()->role !== 'umkm') {
            return response()->json(['message' => 'Hanya akun UMKM yang dapat menambah produk.'], 403);
        }

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'category'            => 'required|string|max:50',
            'description'         => 'required|string|max:2000',
            'price_usd'           => 'required|numeric|min:0',
            'production_capacity' => 'required|integer|min:1',
            'target_countries'    => 'required|array|min:1',
            'target_countries.*'  => 'string|size:2',
            'certifications'      => 'nullable|array',
            'certifications.*'    => 'string|max:50',
            'images'              => 'nullable|array|max:5',
            'images.*'            => 'image|mimes:jpeg,png,webp|max:2048',
        ]);

        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products/' . auth('api')->id(), 'public');
                $imageUrls[] = asset('storage/' . $path);
            }
        }

        $product = Product::create([
            'user_id'             => auth('api')->id(),
            'name'                => $validated['name'],
            'category'            => $validated['category'],
            'description'         => $validated['description'],
            'price_usd'           => $validated['price_usd'],
            'production_capacity' => $validated['production_capacity'],
            'target_countries'    => $validated['target_countries'],
            'certifications'      => $validated['certifications'] ?? [],
            'images'              => $imageUrls,
            'status'              => 'published',
        ]);

        return response()->json([
            'product_id' => $product->id,
            'status'     => $product->status,
        ], 201);
    }

    // PUT /api/v1/catalog/products/:id
    public function update(Request $request, string $id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth('api')->id())
            ->firstOrFail();

        $validated = $request->validate([
            'name'                => 'sometimes|string|max:255',
            'description'         => 'sometimes|string|max:2000',
            'price_usd'           => 'sometimes|numeric|min:0',
            'production_capacity' => 'sometimes|integer|min:1',
            'target_countries'    => 'sometimes|array',
            'certifications'      => 'sometimes|array',
            'status'              => 'sometimes|in:published,draft',
        ]);

        $product->update($validated);

        return response()->json(['message' => 'Produk berhasil diperbarui.', 'product' => $product]);
    }

    // DELETE /api/v1/catalog/products/:id
    public function destroy(string $id)
    {
        $product = Product::where('id', $id)
            ->where('user_id', auth('api')->id())
            ->firstOrFail();

        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }
}
