<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Setting;

class FinalScoreService
{
    /**
     * Calculate and save candidate final score based on weights.
     */
    public function calculateCandidateFinalScore(Candidate $candidate): Candidate
    {
        $psychotestWeight = floatval(Setting::get('psychotest_weight', 60)) / 100;
        $interviewWeight = floatval(Setting::get('interview_weight', 40)) / 100;

        $psychoScore = floatval($candidate->final_psychotest_score ?? 0);
        $interviewScore = floatval($candidate->final_interview_score ?? 0);

        if ($candidate->final_psychotest_score !== null && $candidate->final_interview_score !== null) {
            $finalScore = round(($psychoScore * $psychotestWeight) + ($interviewScore * $interviewWeight), 2);
            $candidate->final_score = $finalScore;
            $candidate->save();

            // Re-calculate ranks for this vacancy / position
            $rankingService = app(RankingService::class);
            $rankingService->recalculateRanksForVacancy($candidate->vacancy_id);
        }

        return $candidate;
    }

    /**
     * Recalculate all final scores for all candidates with both scores.
     */
    public function recalculateAllFinalScores(): void
    {
        $candidates = Candidate::whereNotNull('final_psychotest_score')
            ->whereNotNull('final_interview_score')
            ->get();

        foreach ($candidates as $candidate) {
            $this->calculateCandidateFinalScore($candidate);
        }
    }
}
