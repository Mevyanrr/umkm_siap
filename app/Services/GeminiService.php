<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function predictExportReadiness(
        array $answers,
        string $productCategory,
        string $targetCountry,
        int $score
    ): array {
        $answersText = collect($answers)
            ->filter(fn($a) => !str_starts_with($a['id'] ?? '', 'q0_'))
            ->map(fn($a) => "- [{$a['id']}]: {$a['value']}")
            ->join("\n");

        $prompt = <<<PROMPT
Kamu adalah konsultan ekspor UMKM Indonesia yang berpengalaman.

Berdasarkan data berikut, berikan analisis kesiapan ekspor dalam format JSON:

**Profil UMKM:**
- Kategori produk: {$productCategory}
- Negara tujuan ekspor: {$targetCountry}
- Skor assessment: {$score}/100

**Jawaban kuis kesiapan ekspor (id: nilai):**
{$answersText}

Keterangan ID pertanyaan:
- q1: NIB, q2: SNI, q3: NPWP, q4: Halal
- q5: kapasitas produksi/bulan, q6: bisa MOQ 500+, q7: punya SOP
- q8: pernah ekspor, q9: familiar kepabeanan (1-5), q10: punya buyer

Berikan respons HANYA dalam format JSON berikut (tanpa markdown, tanpa teks tambahan):
{
  "export_probability": <angka 0.0-1.0>,
  "estimated_readiness_months": <integer, 0 jika sudah siap>,
  "risk_factors": ["<risiko 1>", "<risiko 2>"],
  "strengths": ["<kekuatan 1>", "<kekuatan 2>"],
  "narrative": "<paragraf analisis 2-3 kalimat dalam Bahasa Indonesia>",
  "recommended_certifications": ["<sertifikasi yang dibutuhkan>"],
  "priority_actions": [
    {"priority": "high|medium|low", "task": "<deskripsi tugas>"}
  ]
}
PROMPT;

        return $this->callGemini($prompt);
    }

   public function getMarketIntelligence(
        string $productCategory,
        ?int $assessmentScore = null,
        ?string $readinessLevel = null,
        array $strengths = []
    ): array {
        // DIET KETAT + VALID: Menggunakan panduan tipe data agar Gemini paham tugasnya
        $prompt = <<<PROMPT
Berikan analisis singkat market intelligence ekspor untuk kategori produk: {$productCategory}.
Konteks UMKM - Skor: {$assessmentScore}/100, Level kesiapan: {$readinessLevel}.

Berikan respons HANYA dalam format JSON murni mengikuti struktur ini tanpa teks tambahan luar:
{
  "recommended_countries": [
    {
      "country": "<nama negara tujuan>",
      "country_code": "<kode ISO 2 huruf>",
      "match_score": 0.85,
      "reason": "<1 kalimat alasan kecocokan>",
      "entry_difficulty": "easy|medium|hard",
      "estimated_market_size_usd": "<contoh: 500M>",
      "key_requirements": ["<syarat utama 1>"]
    }
  ],
  "global_trends": [
    {
      "trend": "<nama tren pasar>",
      "impact": "positive|negative|neutral",
      "description": "<deskripsi singkat tren>"
    }
  ],
  "export_opportunities": [
    {
      "opportunity": "<judul peluang>",
      "description": "<penjelasan singkat>",
      "urgency": "high|medium|low"
    }
  ],
  "competitor_landscape": {
    "main_competitor_countries": ["<negara pesaing>"],
    "indonesia_advantages": "<keunggulan produk indonesia>",
    "differentiation_tips": ["<tips bersaing 1>"]
  },
  "narrative_summary": "<ringkasan analisis 2 kalimat dalam Bahasa Indonesia>",
  "recommended_starting_country": "<1 nama negara terbaik untuk mulai>"
}
PROMPT;

        // Token dipangkas aman ke 1000 agar hemat kuota harian/menit
        return $this->callGemini($prompt, maxTokens: 1000);
    }

    private function callGemini(string $prompt, int $maxTokens = 1500): array
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}?key={$this->apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.3,
                        'maxOutputTokens' => $maxTokens,
                        // PAKSA GEMINI MENGEMBALIKAN JSON MURNI NATIVE
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            if ($response->status() === 429) {
                Log::warning('Gemini rate limit hit', ['body' => $response->body()]);
                throw new \Exception('Gemini API rate limit. Coba beberapa saat lagi.');
            }

            if ($response->failed()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \Exception("Gemini API error: {$response->status()}");
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            if (empty($text)) {
                throw new \Exception('Gemini mengembalikan respons kosong.');
            }

            // Karena sudah pakai responseMimeType, text dijamin JSON murni tanpa ```json
            $decoded = json_decode(trim($text), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gemini JSON parse error', ['raw' => $text]);
                throw new \Exception('Respons AI tidak bisa di-parse sebagai JSON.');
            }

            return $decoded;
        } catch (\Exception $e) {
            Log::error('GeminiService error: ' . $e->getMessage());
            throw $e;
        }
    }
}
