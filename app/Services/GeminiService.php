<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private array $apiKeys;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $keys = [];
        $i = 1;
        while (true) {
            $key = env("GEMINI_API_KEY_{$i}");
            if (empty($key)) break;
            $keys[] = $key;
            $i++;
        }

        if (empty($keys)) {
            $legacy = array_filter([
                env('GEMINI_API_KEY'),
                config('services.gemini.api_key'),
            ]);
            $keys = array_values($legacy);
        }

        if (empty($keys)) {
            throw new \RuntimeException('Tidak ada Gemini API key yang dikonfigurasi di .env.');
        }

        shuffle($keys);
        $this->apiKeys = $keys;

        Log::info('GeminiService: ' . count($this->apiKeys) . ' API key tersedia.');
    }

    // -------------------------------------------------------------------------
    // PUBLIC: Assessment kesiapan ekspor
    // -------------------------------------------------------------------------

    public function predictExportReadiness(
        array  $answers,
        string $productCategory,
        string $targetCountry,
        int    $score
    ): array {
        if ($this->useMock()) {
            return $this->mockAssessment($productCategory, $score);
        }

        $a    = collect($answers)->filter(fn($x) => !str_starts_with($x['id'] ?? '', 'q0_'));
        $nib  = $a->firstWhere('id', 'q1')['value']  ?? '?';
        $sni  = $a->firstWhere('id', 'q2')['value']  ?? '?';
        $npwp = $a->firstWhere('id', 'q3')['value']  ?? '?';
        $halal= $a->firstWhere('id', 'q4')['value']  ?? '?';
        $cap  = $a->firstWhere('id', 'q5')['value']  ?? '?';
        $moq  = $a->firstWhere('id', 'q6')['value']  ?? '?';
        $sop  = $a->firstWhere('id', 'q7')['value']  ?? '?';
        $exp  = $a->firstWhere('id', 'q8')['value']  ?? '?';
        $cus  = $a->firstWhere('id', 'q9')['value']  ?? '?';
        $buyer= $a->firstWhere('id', 'q10')['value'] ?? '?';

        $prompt = <<<PROMPT
Analisis kesiapan ekspor UMKM Indonesia.
Produk: {$productCategory}, Target: {$targetCountry}, Skor: {$score}/100.
Data: NIB={$nib}, SNI={$sni}, NPWP={$npwp}, Halal={$halal},
Kapasitas={$cap}/bln, MOQ500={$moq}, SOP={$sop},
PernhEkspor={$exp}, Kepabeanan={$cus}/5, PunyaBuyer={$buyer}.

Balas HANYA JSON (tanpa markdown, tanpa penjelasan):
{
  "export_probability": 0.0,
  "estimated_readiness_months": 0,
  "risk_factors": [],
  "strengths": [],
  "narrative": "",
  "recommended_certifications": [],
  "priority_actions": [{"priority": "high", "task": ""}]
}
PROMPT;

        return $this->callWithFallback($prompt, maxTokens: 600, fallback: fn() => $this->mockAssessment($productCategory, $score));
    }

    // -------------------------------------------------------------------------
    // PUBLIC: Market intelligence
    // -------------------------------------------------------------------------

    public function getMarketIntelligence(
        string $productCategory,
        ?int   $assessmentScore = null,
        ?string $readinessLevel = null,
        array  $strengths = []
    ): array {
        if ($this->useMock()) {
            return $this->mockMarket($productCategory, $assessmentScore ?? 50);
        }

        $prompt = <<<PROMPT
Market intelligence ekspor Indonesia untuk produk: {$productCategory}.
Skor UMKM: {$assessmentScore}/100.

Balas HANYA JSON (tanpa markdown, tanpa penjelasan):
{
  "recommended_countries": [{
    "country": "", "country_code": "", "match_score": 0.0,
    "reason": "", "entry_difficulty": "easy",
    "estimated_market_size_usd": "", "key_requirements": []
  }],
  "global_trends": [{"trend": "", "impact": "positive", "description": ""}],
  "export_opportunities": [{"opportunity": "", "description": "", "urgency": "high"}],
  "competitor_landscape": {
    "main_competitor_countries": [],
    "indonesia_advantages": "",
    "differentiation_tips": []
  },
  "narrative_summary": "",
  "recommended_starting_country": ""
}
PROMPT;

        return $this->callWithFallback($prompt, maxTokens: 700, fallback: fn() => $this->mockMarket($productCategory, $assessmentScore ?? 50));
    }

    // -------------------------------------------------------------------------
    // INTERNAL: Panggil Gemini dengan rotasi key + fallback otomatis ke mock
    // -------------------------------------------------------------------------

    private function callWithFallback(string $prompt, int $maxTokens, callable $fallback): array
    {
        $totalKeys   = count($this->apiKeys);
        $rateLimited = 0;

        foreach ($this->apiKeys as $index => $apiKey) {
            $keyNum = $index + 1;

            try {
                Log::info("GeminiService: mencoba key {$keyNum}/{$totalKeys}");

                $response = Http::timeout(60)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("{$this->baseUrl}?key={$apiKey}", [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'temperature'     => 0.2,
                            'maxOutputTokens' => $maxTokens,
                        ],
                    ]);

                if ($response->status() === 429) {
                    $rateLimited++;
                    Log::warning("GeminiService: key {$keyNum} rate limit ({$rateLimited}/{$totalKeys})");
                    continue;
                }

                if ($response->failed()) {
                    Log::error("GeminiService: key {$keyNum} HTTP error", [
                        'status' => $response->status(),
                        'body'   => substr($response->body(), 0, 300),
                    ]);
                    continue;
                }

                $text = $response->json('candidates.0.content.parts.0.text');

                if (empty($text)) {
                    Log::warning("GeminiService: key {$keyNum} respons kosong.");
                    continue;
                }

                $text = preg_replace('/^```json\s*/im', '', $text);
                $text = preg_replace('/^```\s*/im', '', $text);
                $text = trim($text);

                $decoded = json_decode($text, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error("GeminiService: JSON parse error pada key {$keyNum}", ['raw' => substr($text, 0, 300)]);
                    continue;
                }

                Log::info("GeminiService: berhasil dengan key {$keyNum}");
                return $decoded;

            } catch (\Exception $e) {
                Log::error("GeminiService: exception pada key {$keyNum}: " . $e->getMessage());
                continue;
            }
        }

        Log::warning("GeminiService: semua {$totalKeys} key gagal (rate limit: {$rateLimited}). Menggunakan mock data.");
        return $fallback();
    }

    private function useMock(): bool
    {
        return (bool) config('services.gemini.use_mock', env('GEMINI_USE_MOCK', false));
    }

    // -------------------------------------------------------------------------
    // MOCK DATA
    // -------------------------------------------------------------------------

    private function mockAssessment(string $category, int $score): array
    {
        $high = $score >= 70;
        return [
            'export_probability'         => $high ? 0.78 : 0.42,
            'estimated_readiness_months' => $high ? 3 : 8,
            'risk_factors'               => $high
                ? ['Persaingan harga ketat di pasar global', 'Fluktuasi nilai tukar']
                : ['Dokumen ekspor belum lengkap', 'Kapasitas produksi perlu ditingkatkan', 'Belum ada sertifikasi internasional'],
            'strengths'                  => $high
                ? ['Kualitas produk kompetitif', 'Sudah memiliki NIB dan NPWP', 'Pengalaman ekspor sebelumnya']
                : ['Produk unik bernilai budaya', 'Pasar lokal sudah terbentuk'],
            'narrative'                  => $high
                ? "Bisnis {$category} Anda menunjukkan kesiapan ekspor yang baik dengan skor {$score}/100. Fokus pada peningkatan kapasitas produksi dan networking dengan buyer internasional."
                : "Bisnis {$category} Anda memiliki potensi ekspor yang menjanjikan, namun masih perlu persiapan. Prioritaskan melengkapi dokumen legal dan meningkatkan kapasitas produksi.",
            'recommended_certifications' => ['Sertifikasi Halal MUI', 'SNI', 'ISO 9001'],
            'priority_actions'           => [
                ['priority' => 'high',   'task' => 'Lengkapi dokumen ekspor (Invoice, Packing List, CoO)'],
                ['priority' => 'high',   'task' => 'Daftarkan produk ke platform ekspor UMKM'],
                ['priority' => 'medium', 'task' => 'Ikuti pelatihan prosedur kepabeanan'],
                ['priority' => 'low',    'task' => 'Bangun branding internasional melalui media sosial'],
            ],
        ];
    }

    /**
     * Mock market intelligence berdasarkan kategori produk DAN skor assessment.
     * Skor dibagi 3 level:
     *   - Tinggi  : >= 75  → pasar premium, target negara maju
     *   - Sedang  : 45–74  → pasar menengah, mulai dari ASEAN + Middle East
     *   - Rendah  : < 45   → pasar paling accessible, fokus ASEAN dulu
     */
    private function mockMarket(string $category, int $score): array
    {
        $cat = strtolower($category);

        // Tentukan level skor
        $level = match(true) {
            $score >= 75 => 'high',
            $score >= 45 => 'medium',
            default      => 'low',
        };

        // ── KOPI ──────────────────────────────────────────────────────────────
        if (str_contains($cat, 'kopi') || str_contains($cat, 'coffee')) {
            $advantages = 'Indonesia adalah eksportir kopi terbesar ke-4 dunia dengan keanekaragaman varietas yang unik.';

            if ($level === 'high') {
                $countries = [
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.95, 'reason' => 'Konsumen Jepang sangat mengapresiasi kopi single-origin premium Indonesia, pasar specialty coffee tumbuh 18% YoY',          'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.2B', 'key_requirements' => ['JAS Certification', 'Halal Certificate', 'Radioactive Testing']],
                    ['country' => 'Amerika Serikat', 'country_code' => 'US', 'match_score' => 0.91, 'reason' => 'Specialty coffee AS tumbuh pesat, konsumen rela bayar premium untuk origin story yang unik',                                'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$8.4B', 'key_requirements' => ['FDA Registration', 'Fair Trade Cert', 'Organic Cert']],
                    ['country' => 'Jerman',          'country_code' => 'DE', 'match_score' => 0.87, 'reason' => 'Pasar Eropa premium menghargai kopi organic dan sustainable, skor Anda cukup tinggi untuk masuk pasar ini',                 'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$2.1B', 'key_requirements' => ['EU Organic Cert', 'Rainforest Alliance', 'UTZ Certified']],
                    ['country' => 'Korea Selatan',   'country_code' => 'KR', 'match_score' => 0.83, 'reason' => 'Tren coffee shop specialty Korea berkembang sangat pesat, demand kopi single-origin tinggi',                               'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$620M', 'key_requirements' => ['KFDA Approval', 'Korean Label']],
                    ['country' => 'Uni Emirat Arab', 'country_code' => 'AE', 'match_score' => 0.79, 'reason' => 'CEPA Indonesia-UAE menghapus tarif, ekspansi ke pasar premium Middle East sangat viable',                                  'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$340M', 'key_requirements' => ['Halal MUI', 'ESMA Registration']],
                ];
                $narrative = "Dengan skor {$score}/100, bisnis kopi Anda siap menembus pasar premium global. Jepang dan Amerika Serikat menawarkan margin tertinggi untuk specialty coffee Indonesia.";
                $starting  = 'Jepang';
            } elseif ($level === 'medium') {
                $countries = [
                    ['country' => 'Uni Emirat Arab', 'country_code' => 'AE', 'match_score' => 0.90, 'reason' => 'CEPA Indonesia-UAE menghapus tarif, permintaan kopi premium terus meningkat, regulasi relatif mudah',                      'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$340M', 'key_requirements' => ['Halal MUI', 'ESMA Registration']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.85, 'reason' => 'Hub perdagangan Asia, banyak importir kopi premium berbasis di Singapura sebagai gateway ke Asia',                         'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$180M', 'key_requirements' => ['SFA Approval', 'Halal']],
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.80, 'reason' => 'Pasar kopi Malaysia tumbuh pesat, kedekatan budaya memudahkan penetrasi pasar',                                            'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$220M', 'key_requirements' => ['Halal JAKIM', 'MOH Malaysia']],
                    ['country' => 'Korea Selatan',   'country_code' => 'KR', 'match_score' => 0.72, 'reason' => 'Tren coffee shop specialty Korea sangat besar, namun perlu standar kualitas lebih ketat',                                  'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$620M', 'key_requirements' => ['KFDA Approval', 'Korean Label']],
                    ['country' => 'Taiwan',          'country_code' => 'TW', 'match_score' => 0.68, 'reason' => 'Pasar kopi Taiwan berkembang pesat, preferensi tinggi untuk kopi Asia origin',                                             'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$290M', 'key_requirements' => ['TFDA Approval', 'Chinese Label']],
                ];
                $narrative = "Dengan skor {$score}/100, UAE dan Singapura adalah pintu masuk terbaik untuk ekspor kopi Anda. Fokus pada sertifikasi Halal dan kualitas konsisten untuk membuka pasar lebih luas.";
                $starting  = 'Uni Emirat Arab';
            } else {
                $countries = [
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.88, 'reason' => 'Pasar paling accessible untuk UMKM kopi pemula, regulasi mudah, budaya serupa',                                           'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$220M', 'key_requirements' => ['Halal JAKIM', 'MOH Malaysia']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.82, 'reason' => 'Entry point strategis, banyak agen importir yang membantu UMKM masuk pasar internasional',                                 'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$180M', 'key_requirements' => ['SFA Approval', 'Halal']],
                    ['country' => 'Brunei',          'country_code' => 'BN', 'match_score' => 0.75, 'reason' => 'Pasar kecil tapi permintaan kopi Indonesia tinggi, regulasi sederhana',                                                    'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$15M',  'key_requirements' => ['Halal MUI', 'BDMOA Approval']],
                    ['country' => 'Filipina',        'country_code' => 'PH', 'match_score' => 0.68, 'reason' => 'Pasar kopi Filipina berkembang, UMKM Indonesia bisa masuk lewat e-commerce cross-border',                                 'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$310M', 'key_requirements' => ['FDA Philippines', 'Halal']],
                ];
                $narrative = "Dengan skor {$score}/100, fokus dulu ke pasar ASEAN yang lebih accessible. Malaysia dan Singapura adalah langkah pertama terbaik sebelum ekspansi ke pasar yang lebih kompetitif.";
                $starting  = 'Malaysia';
            }

        // ── TEKSTIL / BATIK / FASHION ──────────────────────────────────────────
        } elseif (str_contains($cat, 'batik') || str_contains($cat, 'tekstil') || str_contains($cat, 'fashion') || str_contains($cat, 'kerajinan')) {
            $advantages = 'Batik Indonesia diakui UNESCO, memberikan keunggulan branding unik yang tidak bisa ditiru kompetitor.';

            if ($level === 'high') {
                $countries = [
                    ['country' => 'Prancis',         'country_code' => 'FR', 'match_score' => 0.90, 'reason' => 'Paris sebagai ibu kota fashion dunia sangat terbuka untuk tekstil etnik premium berkualitas tinggi',                       'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$1.5B', 'key_requirements' => ['EU Textile Regulation', 'REACH Compliance', 'OEKO-TEX']],
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.88, 'reason' => 'Apresiasi tinggi terhadap kerajinan tangan berkualitas, pasar fashion etnik tumbuh konsisten',                             'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$890M', 'key_requirements' => ['Quality Certificate', 'OEKO-TEX', 'Japanese Label']],
                    ['country' => 'Amerika Serikat', 'country_code' => 'US', 'match_score' => 0.83, 'reason' => 'Komunitas diaspora besar + tren fashion etnik di kalangan milenial membuka peluang besar',                                'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$3.2B', 'key_requirements' => ['FTC Label', 'Customs Bond']],
                    ['country' => 'Belanda',         'country_code' => 'NL', 'match_score' => 0.79, 'reason' => 'Koneksi historis Indonesia-Belanda kuat, komunitas Indonesia besar, gateway ke Eropa',                                    'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$680M', 'key_requirements' => ['EU Regulation', 'CE Mark']],
                    ['country' => 'Australia',       'country_code' => 'AU', 'match_score' => 0.74, 'reason' => 'Komunitas diaspora Indonesia besar, minat terhadap produk budaya Indonesia sangat tinggi',                                 'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$420M', 'key_requirements' => ['AQIS Compliance']],
                ];
                $narrative = "Skor {$score}/100 membuka akses ke pasar fashion premium global. Prancis dan Jepang menawarkan harga terbaik untuk produk tekstil/kerajinan berkualitas tinggi dari Indonesia.";
                $starting  = 'Jepang';
            } elseif ($level === 'medium') {
                $countries = [
                    ['country' => 'Australia',       'country_code' => 'AU', 'match_score' => 0.87, 'reason' => 'Komunitas diaspora Indonesia besar, regulasi mudah, permintaan produk budaya Indonesia tinggi',                           'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$420M', 'key_requirements' => ['AQIS Compliance']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.83, 'reason' => 'Hub fashion Asia Tenggara, banyak boutique dan distributor yang mencari produk unik Indonesia',                            'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$210M', 'key_requirements' => ['AVA Approval']],
                    ['country' => 'Uni Emirat Arab', 'country_code' => 'AE', 'match_score' => 0.78, 'reason' => 'Pasar luxury dan fashion tumbuh pesat di UAE, permintaan produk etnik premium meningkat',                                  'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$520M', 'key_requirements' => ['Halal MUI', 'ESMA']],
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.71, 'reason' => 'Target jangka menengah setelah membangun track record ekspor',                                                             'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$890M', 'key_requirements' => ['Quality Certificate', 'Japanese Label']],
                ];
                $narrative = "Skor {$score}/100 cocok untuk memulai ekspor ke Australia dan Singapura. Bangun reputasi dan track record di pasar ini sebelum masuk ke Eropa dan Amerika.";
                $starting  = 'Australia';
            } else {
                $countries = [
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.85, 'reason' => 'Pasar paling mudah, budaya serupa, permintaan batik dan tekstil Indonesia konsisten',                                     'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$180M', 'key_requirements' => ['Halal JAKIM']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.79, 'reason' => 'Entry point ke pasar internasional, banyak agen ekspor yang membantu UMKM pemula',                                        'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$210M', 'key_requirements' => ['AVA Approval']],
                    ['country' => 'Brunei',          'country_code' => 'BN', 'match_score' => 0.70, 'reason' => 'Pasar kecil tapi permintaan tekstil Indonesia tinggi, persyaratan mudah',                                                 'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$20M',  'key_requirements' => ['Halal MUI']],
                ];
                $narrative = "Dengan skor {$score}/100, mulai dari Malaysia dan Singapura untuk membangun pengalaman ekspor pertama. Lengkapi dokumen dan sertifikasi sebelum ekspansi lebih luas.";
                $starting  = 'Malaysia';
            }

        // ── COKLAT / KAKAO ────────────────────────────────────────────────────
        } elseif (str_contains($cat, 'coklat') || str_contains($cat, 'kakao') || str_contains($cat, 'chocolate')) {
            $advantages = 'Indonesia penghasil kakao terbesar ke-3 dunia dengan varietas Trinitario berkualitas premium dari Sulawesi dan Flores.';

            if ($level === 'high') {
                $countries = [
                    ['country' => 'Amerika Serikat', 'country_code' => 'US', 'match_score' => 0.92, 'reason' => 'Pasar craft chocolate AS senilai $4.1B, konsumen sangat menghargai single-origin premium dari Indonesia',                  'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$4.1B', 'key_requirements' => ['FDA Registration', 'Kosher/Halal', 'Nutrition Label']],
                    ['country' => 'Belgia',          'country_code' => 'BE', 'match_score' => 0.88, 'reason' => 'Pusat industri cokelat dunia, buyer Belgia aktif mencari bahan baku premium dari Indonesia',                               'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$980M', 'key_requirements' => ['EU Food Safety', 'Cocoa Origin Cert', 'EUDR Compliance']],
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.84, 'reason' => 'Konsumen Jepang rela bayar premium untuk cokelat unik, pasar gift chocolate sangat besar',                                'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.8B', 'key_requirements' => ['JAS', 'Halal', 'Japanese Nutrition Label']],
                    ['country' => 'Jerman',          'country_code' => 'DE', 'match_score' => 0.79, 'reason' => 'Permintaan cokelat dark premium dan organic sangat tinggi di Jerman',                                                     'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$2.3B', 'key_requirements' => ['EU Organic', 'Fairtrade', 'EUDR']],
                    ['country' => 'Swiss',           'country_code' => 'CH', 'match_score' => 0.74, 'reason' => 'Pasar premium cokelat paling demanding di dunia, cocok untuk produk Anda yang sudah siap ekspor',                         'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$780M', 'key_requirements' => ['Swiss Food Safety', 'EU Compliance']],
                ];
                $narrative = "Skor {$score}/100 membuka pintu ke pasar cokelat premium global. Amerika Serikat dan Belgia adalah target utama dengan nilai pasar tertinggi untuk kakao/cokelat premium Indonesia.";
                $starting  = 'Amerika Serikat';
            } elseif ($level === 'medium') {
                $countries = [
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.88, 'reason' => 'Hub distribusi Asia, gateway ideal untuk cokelat premium menuju pasar Asia lebih luas',                                   'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$120M', 'key_requirements' => ['SFA Approval', 'Halal']],
                    ['country' => 'Uni Emirat Arab', 'country_code' => 'AE', 'match_score' => 0.83, 'reason' => 'Permintaan cokelat premium dan gift chocolate sangat tinggi, pasar halal terbesar di Timur Tengah',                       'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$290M', 'key_requirements' => ['Halal MUI', 'ESMA Registration']],
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.77, 'reason' => 'Target jangka menengah, pasar cokelat Jepang sangat besar tapi standar kualitas ketat',                                  'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.8B', 'key_requirements' => ['JAS', 'Halal']],
                    ['country' => 'Australia',       'country_code' => 'AU', 'match_score' => 0.72, 'reason' => 'Pasar artisan chocolate Australia tumbuh pesat, terbuka untuk produk unique origin',                                      'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$380M', 'key_requirements' => ['FSANZ Approval', 'Halal']],
                ];
                $narrative = "Skor {$score}/100 cocok untuk memulai ekspor via Singapura sebagai hub distribusi. Bangun hubungan dengan importir dan distributor di sana sebelum masuk langsung ke pasar Eropa atau Amerika.";
                $starting  = 'Singapura';
            } else {
                $countries = [
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.85, 'reason' => 'Pasar cokelat Malaysia berkembang, regulasi mudah, budaya serupa memudahkan negosiasi',                                  'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$160M', 'key_requirements' => ['Halal JAKIM', 'MOH Malaysia']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.78, 'reason' => 'Entry point terbaik untuk cokelat Indonesia menuju pasar regional yang lebih luas',                                       'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$120M', 'key_requirements' => ['SFA Approval']],
                    ['country' => 'Filipina',        'country_code' => 'PH', 'match_score' => 0.70, 'reason' => 'Pasar cokelat Filipina tumbuh, ASEAN Free Trade memudahkan ekspor',                                                       'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$210M', 'key_requirements' => ['FDA Philippines', 'Halal']],
                ];
                $narrative = "Dengan skor {$score}/100, fokus ke pasar ASEAN dahulu. Malaysia adalah titik masuk terbaik — lengkapi sertifikasi Halal dan pastikan kualitas konsisten sebelum ekspansi lebih jauh.";
                $starting  = 'Malaysia';
            }

        // ── MAKANAN & MINUMAN (general) ────────────────────────────────────────
        } elseif (str_contains($cat, 'makanan') || str_contains($cat, 'minuman') || str_contains($cat, 'food') || str_contains($cat, 'beverage')) {
            $advantages = 'Produk makanan Indonesia dikenal kaya rasa dan rempah unik yang tidak dimiliki negara lain.';

            if ($level === 'high') {
                $countries = [
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.89, 'reason' => 'Konsumen Jepang sangat selektif dan menghargai kualitas, margin tinggi untuk produk premium Indonesia',                   'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$2.4B', 'key_requirements' => ['JAS Certification', 'Halal', 'Radioactive Testing']],
                    ['country' => 'Amerika Serikat', 'country_code' => 'US', 'match_score' => 0.84, 'reason' => 'Komunitas Asia besar di AS membuka peluang untuk makanan etnik Indonesia',                                                'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$6.8B', 'key_requirements' => ['FDA Registration', 'Nutrition Facts Label']],
                    ['country' => 'Uni Emirat Arab', 'country_code' => 'AE', 'match_score' => 0.81, 'reason' => 'Hub distribusi Timur Tengah, CEPA memberikan keunggulan tarif untuk produk Indonesia',                                    'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$890M', 'key_requirements' => ['Halal MUI', 'ESMA Registration']],
                    ['country' => 'Australia',       'country_code' => 'AU', 'match_score' => 0.76, 'reason' => 'Komunitas Asia besar di Australia, permintaan makanan Asia autentik terus meningkat',                                     'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.1B', 'key_requirements' => ['FSANZ', 'Halal', 'Country of Origin Label']],
                ];
                $narrative = "Skor {$score}/100 membuka akses ke pasar makanan premium di Jepang dan Amerika. Fokus pada storytelling produk dan sertifikasi internasional untuk memaksimalkan harga jual.";
                $starting  = 'Uni Emirat Arab';
            } elseif ($level === 'medium') {
                $countries = [
                    ['country' => 'Uni Emirat Arab', 'country_code' => 'AE', 'match_score' => 0.88, 'reason' => 'CEPA Indonesia-UAE aktif, pasar halal food terbesar di Timur Tengah, distribusi ke 50+ negara',                           'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$890M', 'key_requirements' => ['Halal MUI', 'ESMA Registration']],
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.84, 'reason' => 'Pasar makanan Indonesia sangat populer, regulasi mudah, distribusi established',                                          'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$420M', 'key_requirements' => ['Halal JAKIM', 'MOH Malaysia']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.79, 'reason' => 'Hub regional, banyak agen yang distribusikan produk ke seluruh Asia dari Singapura',                                      'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$310M', 'key_requirements' => ['SFA Approval', 'Halal']],
                    ['country' => 'Saudi Arabia',    'country_code' => 'SA', 'match_score' => 0.73, 'reason' => 'Pasar halal food Saudi Arabia sangat besar dan terus berkembang',                                                         'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.2B', 'key_requirements' => ['Halal MUI', 'SFDA Approval']],
                ];
                $narrative = "Skor {$score}/100 sangat cocok untuk masuk ke pasar halal food di UAE dan Malaysia. Manfaatkan CEPA Indonesia-UAE untuk keunggulan tarif di pasar Timur Tengah.";
                $starting  = 'Malaysia';
            } else {
                $countries = [
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.90, 'reason' => 'Pasar paling mudah diakses, makanan Indonesia sangat populer, banyak distributor yang membantu',                          'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$420M', 'key_requirements' => ['Halal JAKIM', 'MOH Malaysia']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.82, 'reason' => 'Entry point ideal, regulasi jelas dan relatif mudah dipenuhi untuk UMKM pemula',                                          'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$310M', 'key_requirements' => ['SFA Approval', 'Halal']],
                    ['country' => 'Brunei',          'country_code' => 'BN', 'match_score' => 0.74, 'reason' => 'Pasar kecil tapi sangat accessible, banyak impor makanan dari Indonesia',                                                 'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$45M',  'key_requirements' => ['Halal MUI', 'BDMOA']],
                ];
                $narrative = "Dengan skor {$score}/100, Malaysia adalah langkah pertama terbaik. Fokus pada sertifikasi Halal dan kemasan yang menarik sebelum ekspansi ke pasar yang lebih kompetitif.";
                $starting  = 'Malaysia';
            }

        // ── FURNITUR / ROTAN ───────────────────────────────────────────────────
        } elseif (str_contains($cat, 'furnitur') || str_contains($cat, 'furniture') || str_contains($cat, 'rotan') || str_contains($cat, 'kayu')) {
            $advantages = 'Indonesia adalah eksportir furnitur rotan terbesar di dunia dengan desain yang menggabungkan estetika tradisional dan modern.';

            if ($level === 'high') {
                $countries = [
                    ['country' => 'Amerika Serikat', 'country_code' => 'US', 'match_score' => 0.91, 'reason' => 'Pasar furnitur premium AS terbesar di dunia, permintaan furnitur rotan dan kayu tropis sangat tinggi',                    'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$12B', 'key_requirements' => ['Lacey Act Compliance', 'CARB P2', 'FSC Certification']],
                    ['country' => 'Jerman',          'country_code' => 'DE', 'match_score' => 0.86, 'reason' => 'Pasar furnitur premium Eropa, konsumen Jerman menghargai kualitas dan sustainability',                                    'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$4.2B', 'key_requirements' => ['EU Timber Regulation', 'FSC', 'REACH']],
                    ['country' => 'Belanda',         'country_code' => 'NL', 'match_score' => 0.82, 'reason' => 'Gateway ke Eropa, koneksi historis Indonesia-Belanda, banyak importir furnitur Indonesia',                                'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.8B', 'key_requirements' => ['EU Regulation', 'FSC Cert']],
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.78, 'reason' => 'Desain furnitur natural dan minimalis sangat populer di Jepang',                                                          'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$2.1B', 'key_requirements' => ['JAS', 'Formaldehyde Standard']],
                    ['country' => 'Australia',       'country_code' => 'AU', 'match_score' => 0.74, 'reason' => 'Pasar furnitur outdoor Australia sangat besar, rotan Indonesia dominan',                                                  'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$890M', 'key_requirements' => ['AQIS', 'Fumigation Certificate']],
                ];
                $narrative = "Skor {$score}/100 membuka akses ke pasar furnitur premium global. Amerika Serikat dan Eropa menawarkan margin tertinggi untuk furnitur rotan dan kayu berkualitas Indonesia.";
                $starting  = 'Australia';
            } elseif ($level === 'medium') {
                $countries = [
                    ['country' => 'Australia',       'country_code' => 'AU', 'match_score' => 0.87, 'reason' => 'Pasar furnitur outdoor Australia besar, proses ekspor relatif mudah dari Indonesia',                                     'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$890M', 'key_requirements' => ['AQIS', 'Fumigation Certificate']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.82, 'reason' => 'Hub distribusi furnitur Asia, banyak showroom dan importir berbasis di Singapura',                                       'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$380M', 'key_requirements' => ['Singapore Green Label']],
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.75, 'reason' => 'Permintaan furnitur natural dan eco-friendly sangat tinggi di Jepang',                                                   'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$2.1B', 'key_requirements' => ['JAS', 'Formaldehyde Standard']],
                    ['country' => 'Korea Selatan',   'country_code' => 'KR', 'match_score' => 0.69, 'reason' => 'Tren interior natural Korea tumbuh pesat, furnitur rotan Indonesia mulai populer',                                       'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.1B', 'key_requirements' => ['KC Mark', 'Korean Formaldehyde Std']],
                ];
                $narrative = "Skor {$score}/100 cocok untuk memulai ekspor furnitur ke Australia dan Singapura. Pastikan Fumigation Certificate dan dokumen kayu legal tersedia sebelum pengiriman.";
                $starting  = 'Australia';
            } else {
                $countries = [
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.84, 'reason' => 'Pasar furnitur Malaysia berkembang, akses mudah lewat jalur darat dan laut',                                             'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$280M', 'key_requirements' => ['Fumigation Cert', 'Halal (jika ada finishing)']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.78, 'reason' => 'Entry point ideal, banyak agen yang membantu UMKM furnitur masuk pasar internasional',                                   'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$380M', 'key_requirements' => ['Fumigation Certificate']],
                ];
                $narrative = "Dengan skor {$score}/100, fokus ke Malaysia dan Singapura sebagai pasar pertama. Pastikan dokumen legal kayu dan fumigation certificate siap sebelum ekspor.";
                $starting  = 'Malaysia';
            }

        // ── KOSMETIK / HERBAL ──────────────────────────────────────────────────
        } elseif (str_contains($cat, 'kosmetik') || str_contains($cat, 'herbal') || str_contains($cat, 'skincare') || str_contains($cat, 'kecantikan')) {
            $advantages = 'Indonesia kaya bahan alami herbal tropis yang menjadi tren global beauty industry saat ini.';

            if ($level === 'high') {
                $countries = [
                    ['country' => 'Korea Selatan',   'country_code' => 'KR', 'match_score' => 0.88, 'reason' => 'Hub beauty Asia, K-beauty trend membuka jalan untuk natural skincare Indonesia',                                          'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$3.2B', 'key_requirements' => ['KFDA Registration', 'GMP Certificate', 'Safety Assessment']],
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.84, 'reason' => 'Pasar J-beauty premium, konsumen sangat menghargai produk natural dan clean beauty',                                     'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$4.1B', 'key_requirements' => ['PMDA Registration', 'Quasi-drug License', 'Halal']],
                    ['country' => 'Amerika Serikat', 'country_code' => 'US', 'match_score' => 0.80, 'reason' => 'Pasar clean beauty dan natural skincare AS sangat besar dan terus berkembang',                                            'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$7.8B', 'key_requirements' => ['FDA OTC', 'Cruelty-Free', 'Ingredient List']],
                    ['country' => 'Uni Emirat Arab', 'country_code' => 'AE', 'match_score' => 0.76, 'reason' => 'Pasar beauty halal Timur Tengah sangat besar, permintaan kosmetik natural terus naik',                                   'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.4B', 'key_requirements' => ['Halal MUI', 'ESMA Registration', 'DM Approval']],
                ];
                $narrative = "Skor {$score}/100 membuka peluang di pasar beauty premium Asia dan global. Korea Selatan adalah hub terbaik untuk masuk ke industri kecantikan internasional.";
                $starting  = 'Uni Emirat Arab';
            } elseif ($level === 'medium') {
                $countries = [
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.87, 'reason' => 'Pasar kosmetik halal Malaysia terbesar di ASEAN, regulasi mudah dipahami',                                               'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$580M', 'key_requirements' => ['Halal JAKIM', 'NPRA Registration', 'GMP']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.82, 'reason' => 'Hub beauty Asia Tenggara, banyak distributor kosmetik berbasis di Singapura',                                            'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$340M', 'key_requirements' => ['HSA Registration', 'GMP Certificate']],
                    ['country' => 'Uni Emirat Arab', 'country_code' => 'AE', 'match_score' => 0.77, 'reason' => 'Permintaan kosmetik halal dan natural terus meningkat di Timur Tengah',                                                  'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.4B', 'key_requirements' => ['Halal MUI', 'ESMA Registration']],
                    ['country' => 'Thailand',        'country_code' => 'TH', 'match_score' => 0.71, 'reason' => 'Pasar beauty Thailand berkembang pesat, terbuka untuk produk natural ASEAN',                                             'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$890M', 'key_requirements' => ['FDA Thailand', 'GMP']],
                ];
                $narrative = "Skor {$score}/100 cocok untuk memulai di Malaysia dan Singapura. Pastikan GMP certificate dan registrasi produk sudah siap — ini kunci untuk kosmetik ekspor.";
                $starting  = 'Malaysia';
            } else {
                $countries = [
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.86, 'reason' => 'Pasar kosmetik paling accessible untuk UMKM Indonesia, regulasi halal sudah familiar',                                   'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$580M', 'key_requirements' => ['Halal JAKIM', 'NPRA Registration']],
                    ['country' => 'Brunei',          'country_code' => 'BN', 'match_score' => 0.74, 'reason' => 'Pasar kecil tapi accessible, halal standard Indonesia diterima',                                                          'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$28M',  'key_requirements' => ['Halal MUI', 'BDMOA']],
                ];
                $narrative = "Dengan skor {$score}/100, mulai dari Malaysia untuk membangun track record ekspor kosmetik. Prioritaskan GMP certification dan Halal MUI yang diakui internasional.";
                $starting  = 'Malaysia';
            }

        // ── DEFAULT (kategori lain) ────────────────────────────────────────────
        } else {
            $advantages = 'Indonesia memiliki keunggulan produk tropis yang unik, biaya produksi kompetitif, dan reputasi kualitas yang terus meningkat di pasar global.';

            if ($level === 'high') {
                $countries = [
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.87, 'reason' => 'Konsumen Jepang premium yang menghargai kualitas tinggi, margin penjualan sangat baik',                                  'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.8B', 'key_requirements' => ['JAS Certification', 'Japanese Label', 'Halal']],
                    ['country' => 'Amerika Serikat', 'country_code' => 'US', 'match_score' => 0.82, 'reason' => 'Pasar terbesar dunia, demand produk artisan dan etnik dari Indonesia terus tumbuh',                                      'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$6.2B', 'key_requirements' => ['FDA/CPSC', 'US Customs', 'Country of Origin']],
                    ['country' => 'Uni Emirat Arab', 'country_code' => 'AE', 'match_score' => 0.78, 'reason' => 'CEPA Indonesia-UAE aktif, hub distribusi ke 50+ negara Timur Tengah dan Afrika',                                         'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$720M', 'key_requirements' => ['Halal MUI', 'ESMA Registration']],
                    ['country' => 'Australia',       'country_code' => 'AU', 'match_score' => 0.74, 'reason' => 'Kedekatan geografis, CEPA Indonesia-Australia, komunitas Asia besar',                                                    'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$980M', 'key_requirements' => ['AQIS', 'ACCC Compliance']],
                    ['country' => 'Jerman',          'country_code' => 'DE', 'match_score' => 0.69, 'reason' => 'Pintu masuk ke Eropa, konsumen Jerman menghargai produk berkualitas tinggi dan unik',                                    'entry_difficulty' => 'hard',   'estimated_market_size_usd' => '$2.8B', 'key_requirements' => ['CE Mark', 'EU Regulations', 'German Label']],
                ];
                $narrative = "Skor {$score}/100 menunjukkan kesiapan tinggi untuk ekspor global. Jepang dan UAE adalah target prioritas pertama, diikuti Amerika Serikat untuk pasar jangka panjang.";
                $starting  = 'Uni Emirat Arab';
            } elseif ($level === 'medium') {
                $countries = [
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.87, 'reason' => 'Pasar paling accessible, budaya serupa, regulasi mudah, distribusi established',                                         'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$450M', 'key_requirements' => ['Halal MUI', 'SNI', 'MOH Malaysia']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.83, 'reason' => 'Hub perdagangan Asia Tenggara, gateway ke pasar internasional yang lebih luas',                                          'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$280M', 'key_requirements' => ['AVA/SFA Approval', 'Halal']],
                    ['country' => 'Uni Emirat Arab', 'country_code' => 'AE', 'match_score' => 0.76, 'reason' => 'CEPA Indonesia-UAE memberikan keuntungan tarif, pasar growing dengan daya beli tinggi',                                  'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$620M', 'key_requirements' => ['Halal MUI', 'ESMA']],
                    ['country' => 'Jepang',          'country_code' => 'JP', 'match_score' => 0.70, 'reason' => 'Target jangka menengah setelah membangun track record ekspor yang solid',                                                'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$1.8B', 'key_requirements' => ['JAS', 'Japanese Label']],
                    ['country' => 'Australia',       'country_code' => 'AU', 'match_score' => 0.65, 'reason' => 'CEPA Indonesia-Australia memudahkan akses, komunitas Asia besar sebagai konsumen potensial',                              'entry_difficulty' => 'medium', 'estimated_market_size_usd' => '$980M', 'key_requirements' => ['AQIS Compliance']],
                ];
                $narrative = "Skor {$score}/100 cocok untuk memulai dari Malaysia dan Singapura. Manfaatkan CEPA Indonesia-UAE untuk ekspansi ke Timur Tengah setelah track record terbentuk.";
                $starting  = 'Malaysia';
            } else {
                $countries = [
                    ['country' => 'Malaysia',        'country_code' => 'MY', 'match_score' => 0.88, 'reason' => 'Kedekatan geografis, budaya serupa, regulasi paling mudah untuk UMKM Indonesia pemula',                                  'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$450M', 'key_requirements' => ['Halal MUI', 'SNI']],
                    ['country' => 'Singapura',       'country_code' => 'SG', 'match_score' => 0.80, 'reason' => 'Hub perdagangan Asia Tenggara, gateway ke pasar internasional',                                                          'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$280M', 'key_requirements' => ['AVA Approval', 'Halal']],
                    ['country' => 'Brunei',          'country_code' => 'BN', 'match_score' => 0.71, 'reason' => 'Pasar kecil tapi sangat mudah diakses, standar Indonesia banyak diterima',                                               'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$35M',  'key_requirements' => ['Halal MUI', 'BDMOA']],
                    ['country' => 'Filipina',        'country_code' => 'PH', 'match_score' => 0.65, 'reason' => 'ASEAN Free Trade memudahkan ekspor, pasar berkembang dengan daya beli meningkat',                                        'entry_difficulty' => 'easy',   'estimated_market_size_usd' => '$520M', 'key_requirements' => ['FDA Philippines']],
                ];
                $narrative = "Dengan skor {$score}/100, fokus ke pasar ASEAN terlebih dahulu. Malaysia adalah pilihan terbaik — lengkapi dokumen, bangun reputasi, lalu ekspansi ke pasar yang lebih kompetitif.";
                $starting  = 'Malaysia';
            }
        }

        // Trends dan opportunities juga disesuaikan per level skor
        $trends = $level === 'high'
            ? [
                ['trend' => 'Tren Produk Premium & Artisan',      'impact' => 'positive', 'description' => 'Konsumen global kelas atas makin mencari produk handcrafted dan unique origin — peluang besar untuk produk Indonesia premium.'],
                ['trend' => 'E-commerce Cross-border B2B',        'impact' => 'positive', 'description' => 'Platform seperti Alibaba dan Amazon Business memudahkan UMKM langsung menjangkau buyer internasional tanpa perantara.'],
                ['trend' => 'Sustainability & Ethical Sourcing',   'impact' => 'positive', 'description' => 'Buyer global makin memprioritaskan produk yang sustainable dan traceable — Indonesia punya keunggulan narasi ini.'],
                ['trend' => 'Kenaikan Biaya Logistik Global',     'impact' => 'negative', 'description' => 'Biaya shipping internasional masih tinggi pasca pandemi, mempengaruhi daya saing harga di pasar distant.'],
            ]
            : ($level === 'medium'
                ? [
                    ['trend' => 'Tren Produk Organik & Natural',      'impact' => 'positive', 'description' => 'Konsumen global makin prefer produk organik dan natural, membuka peluang premium untuk produk Indonesia.'],
                    ['trend' => 'E-commerce Cross-border',             'impact' => 'positive', 'description' => 'Marketplace seperti Shopee Regional dan Lazada memudahkan UMKM Indonesia menjangkau konsumen ASEAN.'],
                    ['trend' => 'Halal Economy Tumbuh',                'impact' => 'positive', 'description' => 'Pasar produk halal global terus berkembang, Indonesia dengan sertifikasi Halal MUI punya posisi strategis.'],
                    ['trend' => 'Persaingan dari Vietnam & Thailand',  'impact' => 'negative', 'description' => 'Kompetitor ASEAN makin agresif, perlu diferensiasi produk yang kuat untuk bersaing.'],
                ]
                : [
                    ['trend' => 'ASEAN Free Trade Agreement',          'impact' => 'positive', 'description' => 'AFTA memberikan kemudahan akses pasar ke 9 negara ASEAN tanpa tarif, peluang besar untuk UMKM pemula.'],
                    ['trend' => 'Digitalisasi UMKM Ekspor',            'impact' => 'positive', 'description' => 'Program pemerintah seperti UMKM Go Export memudahkan akses ke pasar internasional dengan pendampingan.'],
                    ['trend' => 'Kenaikan Biaya Logistik',             'impact' => 'negative', 'description' => 'Biaya pengiriman internasional tinggi, perlu strategi bundling dan volume yang cukup untuk efisiensi.'],
                    ['trend' => 'Persaingan Produk Lokal di ASEAN',    'impact' => 'negative', 'description' => 'Produk lokal negara tujuan juga berkembang, perlu keunikan dan kualitas yang jelas terlihat.'],
                ]
            );

        $opportunities = $level === 'high'
            ? [
                ['opportunity' => 'CEPA Indonesia-UAE',              'description' => 'Perjanjian dagang Indonesia-UAE menghapus tarif untuk ratusan produk, akses ke 50+ negara Timur Tengah.',          'urgency' => 'high'],
                ['opportunity' => 'RCEP — 15 Negara Asia',           'description' => 'RCEP memberikan preferential tariff ke pasar Jepang, Korea, China, Australia, dan ASEAN sekaligus.',               'urgency' => 'high'],
                ['opportunity' => 'Indonesia-Australia CEPA',        'description' => 'IA-CEPA memberikan akses preferensial ke pasar Australia yang berdaya beli tinggi.',                                'urgency' => 'medium'],
                ['opportunity' => 'Branding "Made in Indonesia"',    'description' => 'Program LPEI dan Kemendag aktif mempromosikan produk Indonesia di pasar global dengan subsidi promosi.',            'urgency' => 'medium'],
            ]
            : ($level === 'medium'
                ? [
                    ['opportunity' => 'CEPA Indonesia-UAE',          'description' => 'Tarif nol untuk ratusan produk Indonesia ke UAE, gateway ke Timur Tengah dan Afrika.',                              'urgency' => 'high'],
                    ['opportunity' => 'RCEP (15 negara Asia)',        'description' => 'Akses ke pasar ASEAN+6 dengan tarif preferensial, termasuk Jepang dan Korea.',                                     'urgency' => 'high'],
                    ['opportunity' => 'Program UMKM Go Export',      'description' => 'Kemendag dan LPEI menyediakan pendampingan dan subsidi bagi UMKM yang mulai ekspor.',                               'urgency' => 'medium'],
                ]
                : [
                    ['opportunity' => 'AFTA — ASEAN Free Trade',     'description' => 'Ekspor ke 9 negara ASEAN dengan tarif 0-5%, pasar 680 juta konsumen.',                                             'urgency' => 'high'],
                    ['opportunity' => 'Program UMKM Go Export',      'description' => 'Kemendag dan LPEI menyediakan pendampingan gratis untuk UMKM yang baru mulai ekspor.',                             'urgency' => 'high'],
                    ['opportunity' => 'E-commerce Regional',         'description' => 'Lazada, Shopee, dan Tokopedia Regional membuka akses ke konsumen ASEAN tanpa biaya distribusi besar.',             'urgency' => 'medium'],
                ]
            );

        return [
            'recommended_countries'        => $countries,
            'global_trends'                => $trends,
            'export_opportunities'         => $opportunities,
            'competitor_landscape'         => [
                'main_competitor_countries' => $level === 'high'
                    ? ['Vietnam', 'Thailand', 'China', 'India']
                    : ['Vietnam', 'Thailand', 'Malaysia', 'China'],
                'indonesia_advantages'      => $advantages,
                'differentiation_tips'      => $level === 'high'
                    ? [
                        'Tonjolkan keunikan regional dan cerita di balik produk (origin story)',
                        'Investasi pada packaging premium yang mencerminkan nilai produk',
                        'Bangun sertifikasi internasional (Organic, Fair Trade, Halal) sebagai diferensiator',
                        'Manfaatkan platform B2B global (Alibaba, Global Sources) untuk menjangkau buyer premium',
                    ]
                    : ($level === 'medium'
                        ? [
                            'Fokus pada satu keunikan produk yang kuat dan konsisten',
                            'Sertifikasi Halal MUI sebagai keunggulan di pasar Muslim global',
                            'Bangun hubungan langsung dengan importir via pameran dagang virtual',
                            'Manfaatkan media sosial untuk membangun brand awareness internasional',
                        ]
                        : [
                            'Mulai dengan produk terbaik dan paling unik yang dimiliki',
                            'Lengkapi sertifikasi Halal dan SNI sebagai langkah pertama',
                            'Bergabung dengan komunitas UMKM ekspor untuk berbagi pengalaman',
                            'Manfaatkan program pemerintah seperti LPEI dan Kemendag untuk pendampingan',
                        ]
                    ),
            ],
            'narrative_summary'            => $narrative,
            'recommended_starting_country' => $starting,
        ];
    }
}