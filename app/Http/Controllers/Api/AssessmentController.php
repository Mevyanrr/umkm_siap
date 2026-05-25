<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;

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

        $assessmentResult = $this->geminiService->predictExportReadiness(
            answers:         $answers,
            productCategory: $productCategory,
            targetCountry:   'Global',
            score:           $score
        );

        $result = [
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

        session(['last_assessment' => $result]);

        return response()->json($result);
    }

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