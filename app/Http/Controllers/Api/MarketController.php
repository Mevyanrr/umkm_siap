<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketController extends Controller
{
    public function __construct(private GeminiService $geminiService) {}

    public function analyze(Request $request)
    {
        $assessment = session('last_assessment');

        $productCategory = $request->input('product_category')
            ?? $assessment['product_category']
            ?? 'Umum';

        $assessmentScore = $assessment['score'] ?? null;
        $readinessLevel  = $assessment['level'] ?? null;
        $strengths       = $assessment['strengths'] ?? [];

        //CALL Gemini Market Intelligence
        //CALL Gemini Market Intelligence (dengan cache)
        $cacheKey = 'market_' . md5($productCategory . $assessmentScore . $readinessLevel . implode(',', $strengths));

        $marketResult = Cache::remember($cacheKey, 3600, function () use (
            $productCategory,
            $assessmentScore,
            $readinessLevel,
            $strengths
        ) {
            return $this->geminiService->getMarketIntelligence(
                productCategory: $productCategory,
                assessmentScore: $assessmentScore,
                readinessLevel: $readinessLevel,
                strengths: $strengths
            );
        });

        return response()->json([
            'product_category'             => $productCategory,
            'assessment_score'             => $assessmentScore,
            'readiness_level'              => $readinessLevel,
            'recommended_countries'        => $marketResult['recommended_countries'] ?? [],
            'global_trends'                => $marketResult['global_trends'] ?? [],
            'export_opportunities'         => $marketResult['export_opportunities'] ?? [],
            'competitor_landscape'         => $marketResult['competitor_landscape'] ?? [],
            'narrative_summary'            => $marketResult['narrative_summary'] ?? '',
            'recommended_starting_country' => $marketResult['recommended_starting_country'] ?? '',
        ]);
    }

    // GET /api/v1/market/trade-data?hs_code=6211&target_country=JP&year=2024
    public function tradeData(Request $request)
    {
        $validated = $request->validate([
            'hs_code'        => 'required|string|max:10',
            'target_country' => 'nullable|string|size:2',
            'year'           => 'nullable|integer|min:2015|max:2025',
        ]);

        $cacheKey = "trade_data_{$validated['hs_code']}_" .
            ($validated['target_country'] ?? 'WORLD') . "_" .
            ($validated['year'] ?? 2024);

        $data = Cache::remember($cacheKey, 86400, function () use ($validated) {
            return $this->fetchFromITCTradeMap($validated);
        });

        return response()->json($data);
    }

    // GET /api/v1/market/trending-products?category=makanan&limit=10
    public function trendingProducts(Request $request)
    {
        $validated = $request->validate([
            'category' => 'nullable|string|max:50',
            'limit'    => 'nullable|integer|min:1|max:50',
        ]);

        $limit    = $validated['limit'] ?? 10;
        $category = $validated['category'] ?? 'all';

        $data = Cache::remember("trending_{$category}_{$limit}", 43200, function () use ($category, $limit) {
            return $this->fetchTrendingFromBPS($category, $limit);
        });

        return response()->json(['trending' => $data]);
    }

    // GET /api/v1/market/countries
    public function countries()
    {
        return response()->json(['data' => ['ID', 'US', 'JP', 'AU', 'DE']]);
    }

    // GET /api/v1/market/categories
    public function categories()
    {
        return response()->json(['data' => ['makanan', 'tekstil', 'furnitur']]);
    }

    private function fetchFromITCTradeMap(array $params): array
    {
        return [
            'hs_code'           => $params['hs_code'],
            'target_country'    => $params['target_country'] ?? 'WORLD',
            'year'              => $params['year'] ?? 2024,
            'export_volume_usd' => 14200000,
            'top_buyers'        => [
                ['country' => 'JP', 'value_usd' => 3200000, 'growth_pct' => 12],
                ['country' => 'US', 'value_usd' => 2800000, 'growth_pct' => 8],
                ['country' => 'AU', 'value_usd' => 1500000, 'growth_pct' => 15],
            ],
            'trend' => [
                ['year' => 2022, 'value' => 11000000],
                ['year' => 2023, 'value' => 12800000],
                ['year' => 2024, 'value' => 14200000],
            ],
            'source'       => 'ITC Trade Map',
            'last_updated' => '2024-12',
        ];
    }

    private function fetchTrendingFromBPS(string $category, int $limit): array
    {
        $products = [
            ['product' => 'Kopi Arabika',        'category' => 'makanan',  'growth_pct' => 34, 'top_dest' => ['US', 'EU', 'JP']],
            ['product' => 'Minyak Kelapa Sawit', 'category' => 'makanan',  'growth_pct' => 18, 'top_dest' => ['IN', 'PK', 'EU']],
            ['product' => 'Batik Tulis',         'category' => 'tekstil',  'growth_pct' => 22, 'top_dest' => ['JP', 'AU', 'NL']],
            ['product' => 'Produk Rotan',        'category' => 'furnitur', 'growth_pct' => 28, 'top_dest' => ['US', 'DE', 'NL']],
            ['product' => 'Kakao Fermentasi',    'category' => 'makanan',  'growth_pct' => 41, 'top_dest' => ['CH', 'DE', 'BE']],
        ];

        if ($category !== 'all') {
            $products = array_filter($products, fn($p) => $p['category'] === $category);
        }

        return array_slice(array_values($products), 0, $limit);
    }
}
