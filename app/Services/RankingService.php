<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Collection;

class RankingService
{
    /**
     * Get Psychotest Leaderboard with filters.
     */
    public function getPsychotestRankings(?int $vacancyId = null, ?int $positionId = null, ?string $status = null): Collection
    {
        $query = Candidate::with(['user', 'vacancy.position', 'latestAttempt'])
            ->whereNotNull('final_psychotest_score');

        if ($vacancyId) {
            $query->where('vacancy_id', $vacancyId);
        }

        if ($positionId) {
            $query->whereHas('vacancy', function ($q) use ($positionId) {
                $q->where('position_id', $positionId);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Urutkan berdasarkan nilai psikotes DESC, lalu durasi tercepat
        return $query->get()->sort(function (Candidate $a, Candidate $b) {
            if ($a->final_psychotest_score != $b->final_psychotest_score) {
                return $b->final_psychotest_score <=> $a->final_psychotest_score;
            }
            $durationA = $a->latestAttempt?->duration_seconds_used ?? 999999;
            $durationB = $b->latestAttempt?->duration_seconds_used ?? 999999;

            return $durationA <=> $durationB;
        })->values();
    }

    /**
     * Get Final Selection Leaderboard with filters.
     */
    public function getFinalRankings(?int $vacancyId = null, ?int $positionId = null, ?string $finalStatus = null): Collection
    {
        $query = Candidate::with(['user', 'vacancy.position', 'latestAttempt', 'latestInterview'])
            ->whereNotNull('final_score');

        if ($vacancyId) {
            $query->where('vacancy_id', $vacancyId);
        }

        if ($positionId) {
            $query->whereHas('vacancy', function ($q) use ($positionId) {
                $q->where('position_id', $positionId);
            });
        }

        if ($finalStatus) {
            $query->where('final_status', $finalStatus);
        }

        return $query->get()->sort(function (Candidate $a, Candidate $b) {
            // 1. Nilai Akhir Terbesar
            if ($a->final_score != $b->final_score) {
                return $b->final_score <=> $a->final_score;
            }
            // 2. Tie breaker: Nilai Psikotes Terbesar
            if ($a->final_psychotest_score != $b->final_psychotest_score) {
                return $b->final_psychotest_score <=> $a->final_psychotest_score;
            }
            // 3. Tie breaker: Durasi Psikotes Tercepat
            $durationA = $a->latestAttempt?->duration_seconds_used ?? 999999;
            $durationB = $b->latestAttempt?->duration_seconds_used ?? 999999;

            return $durationA <=> $durationB;
        })->values();
    }

    /**
     * Recalculate rank numbers for a specific vacancy.
     */
    public function recalculateRanksForVacancy(int $vacancyId): void
    {
        $candidates = Candidate::where('vacancy_id', $vacancyId)
            ->whereNotNull('final_score')
            ->with('latestAttempt')
            ->get();

        $sorted = $candidates->sort(function (Candidate $a, Candidate $b) {
            if ($a->final_score != $b->final_score) {
                return $b->final_score <=> $a->final_score;
            }
            if ($a->final_psychotest_score != $b->final_psychotest_score) {
                return $b->final_psychotest_score <=> $a->final_psychotest_score;
            }
            $durationA = $a->latestAttempt?->duration_seconds_used ?? 999999;
            $durationB = $b->latestAttempt?->duration_seconds_used ?? 999999;

            return $durationA <=> $durationB;
        })->values();

        $rank = 1;
        foreach ($sorted as $candidate) {
            $candidate->final_rank = $rank++;
            $candidate->save();
        }
    }
}
