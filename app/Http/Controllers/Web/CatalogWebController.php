<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogWebController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search'         => 'nullable|string|max:100',
            'category'       => 'nullable|string|max:50',
            'target_country' => 'nullable|string|max:2',
            'certified'      => 'nullable|string|max:50',
        ]);

        $query = Product::with('seller:id,name,phone,email')
            ->where('status', 'published');

        if (!empty($validated['search'])) {
            $query->where('name', 'like', '%' . $validated['search'] . '%');
        }

        if (!empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }

        if (!empty($validated['target_country'])) {
            $query->whereJsonContains('target_countries', strtoupper($validated['target_country']));
        }

        if (!empty($validated['certified'])) {
            $query->whereJsonContains('certifications', $validated['certified']);
        }

        $products = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('umkm.catalog_b2b', compact('products'));
    }
}