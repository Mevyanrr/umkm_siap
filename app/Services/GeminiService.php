<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function predictExportReadiness(array $answers, string $productCategory, string $targetCountry, int $score): array
    {
        $answersText = collect($answers)->map(fn($a) => "- {$a['question']}: {$a['value']}")->join("\n");

        $prompt = <<<PROMPT
Kamu adalah konsultan ekspor UMKM Indonesia yang berpengalaman.

Berdasarkan data berikut, berikan analisis kesiapan ekspor dalam format JSON:

**Profil UMKM:**
- Kategori produk: {$productCategory}
- Negara tujuan ekspor: {$targetCountry}
- Skor assessment: {$score}/100

**Jawaban kuis kesiapan ekspor:**
{$answersText}

Berikan respons HANYA dalam format JSON berikut (tanpa markdown, tanpa teks tambahan):
{
  "export_probability": <angka 0.0-1.0>,
  "estimated_readiness_months": <integer, 0 jika sudah siap>,
  "risk_factors": ["<risiko 1>", "<risiko 2>"],
  "strengths": ["<kekuatan 1>", "<kekuatan 2>"],
  "narrative": "<paragraf analisis 2-3 kalimat dalam Bahasa Indonesia>",
  "recommended_certifications": ["<sertifikasi yang dibutuhkan untuk target negara>"],
  "priority_actions": [
    {"priority": "high|medium|low", "task": "<deskripsi tugas>"}
  ]
}
PROMPT;

        return $this->callGemini($prompt);
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

    private function callGemini(string $prompt): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}?key={$this->apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.3,
                        'maxOutputTokens' => 1024,
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
                throw new \Exception('Gemini API tidak merespons.');
            }

            $text = $response->json('candidates.0.content.parts.0.text');
            $text = preg_replace('/```json\s*|\s*```/', '', trim($text));

            return json_decode($text, true) ?? throw new \Exception('Respons AI tidak valid.');

        } catch (\Exception $e) {
            Log::error('GeminiService error: ' . $e->getMessage());
            throw $e;
        }
    }
}
