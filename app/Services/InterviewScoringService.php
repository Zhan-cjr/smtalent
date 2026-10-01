<?php

namespace App\Services;

use App\Models\Interview;
use App\Models\InterviewScore;
use Illuminate\Support\Facades\DB;

class InterviewScoringService
{
    public function __construct(
        protected FinalScoreService $finalScoreService
    ) {}

    /**
     * Store or update interview scores and compute average.
     *
     * @param  array<string, float|int>  $aspectScores  Key-value map of aspect name => score (0-100)
     * @param  array<string, mixed>  $interviewData  Notes, strengths, weaknesses, recommendation, etc.
     */
    public function saveInterviewScore(Interview $interview, array $aspectScores, array $interviewData = []): Interview
    {
        return DB::transaction(function () use ($interview, $aspectScores, $interviewData) {
            $totalScore = 0;
            $count = 0;

            // Delete previous scores and re-insert
            $interview->scores()->delete();

            foreach ($aspectScores as $aspectName => $score) {
                $numericScore = floatval($score);
                InterviewScore::create([
                    'interview_id' => $interview->id,
                    'aspect_name' => $aspectName,
                    'score' => $numericScore,
                ]);

                $totalScore += $numericScore;
                $count++;
            }

            $averageScore = $count > 0 ? round($totalScore / $count, 2) : 0.00;

            $interview->update([
                'average_score' => $averageScore,
                'status' => 'completed',
                'notes' => $interviewData['notes'] ?? $interview->notes,
                'strengths' => $interviewData['strengths'] ?? $interview->strengths,
                'weaknesses' => $interviewData['weaknesses'] ?? $interview->weaknesses,
                'recommendation' => $interviewData['recommendation'] ?? $interview->recommendation,
            ]);

            $candidate = $interview->candidate;
            if ($candidate) {
                $candidate->final_interview_score = $averageScore;
                $candidate->status = 'LULUS_INTERVIEW';
                $candidate->save();

                // Hitung Nilai Akhir secara otomatis
                $this->finalScoreService->calculateCandidateFinalScore($candidate);
            }

            return $interview;
        });
    }
}
