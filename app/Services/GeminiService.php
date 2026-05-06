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
        $contextFromAssessment = '';

        if ($assessmentScore !== null) {
            $strengthsList = collect($strengths)
                ->map(fn($s) => "- {$s}")
                ->join("\n");

            $contextFromAssessment = <<<CONTEXT

**Data dari Hasil Assessment UMKM:**
- Skor kesiapan ekspor: {$assessmentScore}/100
- Level kesiapan: {$readinessLevel}
- Kekuatan utama bisnis:
{$strengthsList}
CONTEXT;
        }

        $prompt = <<<PROMPT
Kamu adalah analis pasar ekspor internasional yang ahli dalam produk UMKM Indonesia.

Berikan analisis market intelligence untuk UMKM yang ingin ekspor, berdasarkan data berikut:

**Kategori Produk:** {$productCategory}
{$contextFromAssessment}

Berikan respons HANYA dalam format JSON berikut (tanpa markdown, tanpa teks tambahan):
{
  "recommended_countries": [
    {
      "country": "<nama negara>",
      "country_code": "<kode ISO 2 huruf>",
      "match_score": <angka 0.0-1.0>,
      "reason": "<alasan singkat 1 kalimat>",
      "entry_difficulty": "easy|medium|hard",
      "estimated_market_size_usd": "<contoh: 2.3B>",
      "key_requirements": ["<syarat 1>", "<syarat 2>"]
    }
  ],
  "global_trends": [
    {
      "trend": "<nama tren>",
      "impact": "positive|negative|neutral",
      "description": "<deskripsi singkat>"
    }
  ],
  "export_opportunities": [
    {
      "opportunity": "<judul peluang>",
      "description": "<penjelasan 1-2 kalimat>",
      "urgency": "high|medium|low"
    }
  ],
  "competitor_landscape": {
    "main_competitors": ["<negara pesaing 1>", "<negara pesaing 2>"],
    "indonesia_advantage": "<keunggulan produk Indonesia>",
    "differentiation_tips": ["<tips 1>", "<tips 2>"]
  },
  "narrative_summary": "<ringkasan 3-4 kalimat dalam Bahasa Indonesia>",
  "recommended_starting_country": "<1 negara terbaik untuk mulai ekspor>"
}
PROMPT;

        return $this->callGemini($prompt, maxTokens: 2048);
    }

    public function chatAboutAssessment(string $question, array $assessmentContext): array
    {
        $context = json_encode($assessmentContext, JSON_UNESCAPED_UNICODE);

        $prompt = <<<PROMPT
Kamu adalah konsultan ekspor UMKM Indonesia. Jawab pertanyaan berikut berdasarkan konteks assessment.

Konteks assessment:
{$context}

Pertanyaan pengguna: {$question}

Berikan respons HANYA dalam format JSON berikut:
{
  "answer": "<jawaban dalam Bahasa Indonesia, maksimal 3 paragraf>",
  "suggested_resources": ["<referensi atau lembaga yang relevan>"]
}
PROMPT;

        return $this->callGemini($prompt);
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
                ],
            ]);

        // Langsung throw, JANGAN retry kalau 429
        if ($response->status() === 429) {
            Log::warning('Gemini rate limit hit');
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

        $text    = preg_replace('/```json\s*|\s*```/', '', trim($text));
        $decoded = json_decode($text, true);

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
