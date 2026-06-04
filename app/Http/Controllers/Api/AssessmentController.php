<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function __construct(private GeminiService $geminiService) {}

    public function questions()
    {
        $categories = [
            [
                'id'    => 'product_profile',
                'label' => 'Profil Produk',
                'questions' => [
                    [
                        'id'      => 'q0_product_category',
                        'text'    => 'Apa kategori utama produk yang ingin Anda ekspor?',
                        'type'    => 'select',
                        'options' => [
                            'Makanan & Minuman',
                            'Kerajinan Tangan',
                            'Tekstil & Fashion',
                            'Kosmetik & Herbal',
                            'Furnitur & Dekorasi',
                            'Pertanian & Perkebunan',
                            'Elektronik & Teknologi',
                            'Lainnya',
                        ],
                    ],
                    [
                        'id'   => 'q0_product_name',
                        'text' => 'Sebutkan nama atau deskripsi singkat produk Anda',
                        'type' => 'text',
                    ],
                ],
            ],
            [
                'id'    => 'legal_docs',
                'label' => 'Kelengkapan Dokumen Legal',
                'questions' => [
                    ['id' => 'q1', 'text' => 'Apakah usaha Anda sudah memiliki NIB (Nomor Induk Berusaha)?',       'type' => 'boolean'],
                    ['id' => 'q2', 'text' => 'Apakah produk Anda sudah bersertifikat SNI?',                        'type' => 'boolean'],
                    ['id' => 'q3', 'text' => 'Apakah Anda memiliki NPWP aktif?',                                   'type' => 'boolean'],
                    ['id' => 'q4', 'text' => 'Apakah produk Anda sudah memiliki sertifikat Halal (jika berlaku)?', 'type' => 'boolean'],
                ],
            ],
            [
                'id'    => 'production_capacity',
                'label' => 'Kapasitas Produksi',
                'questions' => [
                    ['id' => 'q5', 'text' => 'Berapa kapasitas produksi Anda per bulan (dalam unit)?',    'type' => 'number'],
                    ['id' => 'q6', 'text' => 'Apakah Anda bisa memenuhi MOQ (minimum order) 500+ unit?', 'type' => 'boolean'],
                    ['id' => 'q7', 'text' => 'Apakah Anda memiliki SOP produksi yang terdokumentasi?',   'type' => 'boolean'],
                ],
            ],
            [
                'id'    => 'export_knowledge',
                'label' => 'Pengetahuan Ekspor',
                'questions' => [
                    ['id' => 'q8',  'text' => 'Apakah Anda sudah pernah mengekspor produk sebelumnya?',       'type' => 'boolean'],
                    ['id' => 'q9',  'text' => 'Seberapa familiar Anda dengan prosedur kepabeanan?',           'type' => 'scale', 'min' => 1, 'max' => 5],
                    ['id' => 'q10', 'text' => 'Apakah Anda sudah memiliki buyer potensial di negara tujuan?', 'type' => 'boolean'],
                ],
            ],
        ];

        return response()->json(['categories' => $categories]);
    }

    public function submit(Request $request)
    {
        $answers = $request->input('answers');

        $productCategory = collect($answers)
            ->firstWhere('id', 'q0_product_category')['value'] ?? 'Umum';

        $productName = collect($answers)
            ->firstWhere('id', 'q0_product_name')['value'] ?? '';

        $score = $this->calculateScore($answers);
        $level = $this->getReadinessLevel($score);

        $assessmentResult = app()->isLocal()
            ? $this->getDummyExportReadiness($score)
            : $this->geminiService->predictExportReadiness(
                answers:         $answers,
                productCategory: $productCategory,
                targetCountry:   'Global',
                score:           $score
            );

        // Simpan ke database
        $assessment = Assessment::create([
            'user_id'          => Auth::id(),
            'answers'          => $answers,
            'product_category' => $productCategory,
            'target_country'   => 'Global',
            'score'            => $score,
            'level'            => $level,
            'ai_prediction'    => $assessmentResult,
        ]);

        $result = [
            'assessment_id'              => $assessment->id,
            'score'                      => $score,
            'level'                      => $level,
            'product_category'           => $productCategory,
            'product_name'               => $productName,
            'strengths'                  => $assessmentResult['strengths']                  ?? [],
            'risk_factors'               => $assessmentResult['risk_factors']               ?? [],
            'narrative'                  => $assessmentResult['narrative']                  ?? '',
            'priority_actions'           => $assessmentResult['priority_actions']           ?? [],
            'recommended_certifications' => $assessmentResult['recommended_certifications'] ?? [],
        ];

        // Tetap simpan session untuk halaman result
        session(['last_assessment' => $result]);

        return response()->json($result);
    }

    private function getDummyExportReadiness(int $score): array
    {
        $isHigh = $score >= 70;

        return [
            'export_probability'         => $isHigh ? 0.80 : 0.55,
            'estimated_readiness_months' => $isHigh ? 1 : 4,
            'strengths'                  => $isHigh
                ? [
                    'Sudah memiliki NIB, NPWP, dan dokumen legal lengkap',
                    'Kapasitas produksi memadai dan bisa memenuhi MOQ 500+ unit',
                    'Pernah melakukan ekspor sebelumnya',
                    'SOP produksi sudah terdokumentasi dengan baik',
                ]
                : [
                    'Sudah memiliki NIB dan NPWP aktif',
                    'Kapasitas produksi cukup untuk memulai ekspor skala kecil',
                ],
            'risk_factors'               => $isHigh
                ? [
                    'Belum memiliki buyer tetap di negara tujuan',
                    'Perlu peningkatan sertifikasi untuk pasar premium',
                ]
                : [
                    'Belum memiliki sertifikat SNI',
                    'Kurang familiar dengan prosedur kepabeanan',
                    'Belum ada buyer potensial di negara tujuan',
                    'SOP produksi perlu diperkuat',
                ],
            'narrative'                  => $isHigh
                ? 'UMKM Anda berada dalam posisi yang sangat baik untuk memulai ekspor dalam waktu dekat. '
                    . 'Kelengkapan dokumen legal dan pengalaman ekspor sebelumnya menjadi keunggulan kompetitif yang signifikan. '
                    . 'Fokus selanjutnya adalah memperkuat jaringan buyer internasional dan meningkatkan sertifikasi produk.'
                : 'UMKM Anda menunjukkan fondasi yang cukup kuat untuk memulai persiapan ekspor. '
                    . 'Namun, masih diperlukan beberapa langkah penting terutama di bidang sertifikasi produk '
                    . 'dan pemahaman prosedur ekspor agar bisa bersaing di pasar internasional.',
            'recommended_certifications' => [
                'SNI (Standar Nasional Indonesia)',
                'Sertifikat Halal MUI',
                'ISO 9001:2015 – Manajemen Mutu',
            ],
            'priority_actions'           => [
                ['priority' => 'high',   'task' => 'Urus sertifikasi SNI untuk produk utama Anda di BSN'],
                ['priority' => 'high',   'task' => 'Ikuti pelatihan prosedur ekspor di LPEI atau Dinas Perdagangan setempat'],
                ['priority' => 'medium', 'task' => 'Daftarkan produk di platform B2B internasional (Alibaba, Global Sources, TradeKey)'],
                ['priority' => 'medium', 'task' => 'Buat katalog produk bilingual (Indonesia-Inggris) dengan spesifikasi teknis'],
                ['priority' => 'low',    'task' => 'Pertimbangkan sertifikasi Halal untuk membuka pasar Timur Tengah dan Malaysia'],
            ],
        ];
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    private function getReadinessLevel(int $score): string
    {
        return match (true) {
            $score >= 80 => 'Siap Ekspor',
            $score >= 60 => 'Siap Ekspor dengan Beberapa Perbaikan',
            $score >= 40 => 'Perlu Persiapan Lebih Lanjut',
            default      => 'Belum Siap Ekspor',
        };
    }

    private function calculateScore(array $answers): int
    {
        $score  = 0;
        $weight = [
            'q1'  => 10,
            'q2'  => 10,
            'q3'  => 8,
            'q4'  => 7,
            'q5'  => 5,
            'q6'  => 10,
            'q7'  => 8,
            'q8'  => 12,
            'q9'  => 15,
            'q10' => 15,
        ];

        foreach ($answers as $answer) {
            $id    = $answer['id']    ?? '';
            $value = $answer['value'] ?? null;

            if (!isset($weight[$id])) continue;

            if ($id === 'q9') {
                $score += (int) round(($value / 5) * $weight[$id]);
            } elseif ($id === 'q5') {
                $score += ($value >= 500)
                    ? $weight[$id]
                    : (int) round(($value / 500) * $weight[$id]);
            } elseif (in_array($value, [true, 'true', 1, '1'], true)) {
                $score += $weight[$id];
            }
        }

        return min($score, 100);
    }
}