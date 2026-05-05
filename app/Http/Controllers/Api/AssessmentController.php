<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function __construct(private GeminiService $gemini) {}

    public function questions()
    {
        $categories = [
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
        $validated = $request->validate([
            'answers'               => 'required|array|min:5',
            'answers.*.question_id' => 'required|string',
            'answers.*.question'    => 'required|string',
            'answers.*.value'       => 'required',
            'product_category'      => 'required|string|max:100',
            'target_country'        => 'required|string|max:100',
        ]);

        $score = $this->calculateScore($validated['answers']);

        $aiPrediction = $this->gemini->predictExportReadiness(
            $validated['answers'],
            $validated['product_category'],
            $validated['target_country'],
            $score
        );

        $level = match(true) {
            $score >= 75 => 'Siap Ekspor',
            $score >= 50 => 'Siap Bersyarat',
            default      => 'Belum Siap',
        };

        $assessment = Assessment::create([
            'user_id'          => auth('api')->id(),
            'answers'          => $validated['answers'],
            'product_category' => $validated['product_category'],
            'target_country'   => $validated['target_country'],
            'score'            => $score,
            'level'            => $level,
            'ai_prediction'    => $aiPrediction,
        ]);

        return response()->json([
            'assessment_id' => $assessment->id,
            'score'         => $score,
            'level'         => $level,
            'ai_prediction' => $aiPrediction,
            'todo_list'     => $aiPrediction['priority_actions'] ?? [],
        ]);
    }

    public function chat(Request $request)
    {
        $validated = $request->validate([
            'assessment_id' => 'required|string',
            'question'      => 'required|string|max:500',
        ]);

        $assessment = Assessment::where('id', $validated['assessment_id'])
            ->where('user_id', auth('api')->id())
            ->firstOrFail();

        $answer = $this->gemini->chatAboutAssessment(
            $validated['question'],
            [
                'score'            => $assessment->score,
                'level'            => $assessment->level,
                'product_category' => $assessment->product_category,
                'target_country'   => $assessment->target_country,
                'ai_prediction'    => $assessment->ai_prediction,
            ]
        );

        return response()->json($answer);
    }

    private function calculateScore(array $answers): int
    {
        $trueCount = collect($answers)->filter(fn($a) => $a['value'] === true)->count();
        $totalBool = collect($answers)->filter(fn($a) => is_bool($a['value']))->count();
        $scaleAvg  = collect($answers)->filter(fn($a) => is_numeric($a['value']) && $a['value'] <= 5)->avg('value') ?? 3;

        $boolScore  = $totalBool > 0 ? ($trueCount / $totalBool) * 70 : 35;
        $scaleScore = (($scaleAvg - 1) / 4) * 30;

        return (int) round($boolScore + $scaleScore);
    }
}
