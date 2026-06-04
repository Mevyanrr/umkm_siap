<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function umkm()
    {
        $user = Auth::user();

        $assessment = Assessment::where('user_id', $user->id)
            ->latest()
            ->first();

        $score = $assessment?->score ?? 0;
        $level = $assessment?->level ?? null;

        $todoProgress = $this->calculateTodoProgress($user, $assessment);

        return view('dashboard.umkm', compact('score', 'level', 'assessment', 'todoProgress'));
    }

    private function calculateTodoProgress($user, $assessment): array
    {
        $assessmentSelesai = $assessment !== null;

        $profilLengkap = !empty($user->name)
                      && !empty($user->email)
                      && !empty($user->phone);

        $produkCount = $user->products()->count();
        $produkCukup = $produkCount >= 3; // ← fix: minimal 3

        $adaSertifikasi = false;
        if ($assessment && !empty($assessment->answers)) {
            $answers        = collect($assessment->answers);
            $sni            = $answers->firstWhere('id', 'q2')['value'] ?? false;
            $halal          = $answers->firstWhere('id', 'q4')['value'] ?? false;
            $adaSertifikasi = in_array($sni,   [true, 'true', 1, '1'], true)
                           || in_array($halal, [true, 'true', 1, '1'], true);
        }

        $adaBuyer = false;
        if ($assessment && !empty($assessment->answers)) {
            $answers  = collect($assessment->answers);
            $q10      = $answers->firstWhere('id', 'q10')['value'] ?? false;
            $adaBuyer = in_array($q10, [true, 'true', 1, '1'], true);
        }

        $done  = collect([$profilLengkap, $assessmentSelesai, $produkCukup, $adaSertifikasi, $adaBuyer])
                    ->filter()
                    ->count();
        $total = 5;
        $pct   = (int) round(($done / $total) * 100);

        return [
            'profil_lengkap'     => $profilLengkap,
            'assessment_selesai' => $assessmentSelesai,
            'produk_cukup'       => $produkCukup,
            'ada_sertifikasi'    => $adaSertifikasi,
            'ada_buyer'          => $adaBuyer,
            'produk_count'       => $produkCount,
            'done'               => $done,
            'total'              => $total,
            'pct'                => $pct,
        ];
    }
}