<?php

namespace App\Services;

use App\Models\TestAttempt;
use Illuminate\Support\Facades\DB;

class PsychotestScoringService
{
    /**
     * Calculate and finalize test attempt scores.
     */
    public function scoreAttempt(TestAttempt $attempt): TestAttempt
    {
        return DB::transaction(function () use ($attempt) {
            $attempt->load(['testPackage', 'answers.question.options', 'candidate']);

            $answers = $attempt->answers;
            $package = $attempt->testPackage;

            $multipleChoiceTotal = 0;
            $multipleChoiceCorrect = 0;
            $multipleChoiceWrong = 0;
            $totalAnswered = 0;
            $aspectScoresSum = []; // e.g. ['Ketelitian' => ['sum' => 15, 'count' => 3]]

            foreach ($answers as $answer) {
                $question = $answer->question;
                if (! $question) {
                    continue;
                }

                if ($question->type === 'multiple_choice') {
                    $multipleChoiceTotal++;
                    if ($answer->selected_option_id) {
                        $totalAnswered++;
                        $selectedOption = $question->options->firstWhere('id', $answer->selected_option_id);
                        $isCorrect = $selectedOption && $selectedOption->is_correct;
                        $answer->is_correct = $isCorrect;
                        $answer->save();

                        if ($isCorrect) {
                            $multipleChoiceCorrect++;
                        } else {
                            $multipleChoiceWrong++;
                        }
                    } else {
                        $answer->is_correct = false;
                        $answer->save();
                    }
                } elseif ($question->type === 'likert_scale') {
                    if ($answer->likert_value) {
                        $totalAnswered++;
                        $aspect = $question->aspect ?: 'Umum';
                        if (! isset($aspectScoresSum[$aspect])) {
                            $aspectScoresSum[$aspect] = ['sum' => 0, 'count' => 0];
                        }
                        $aspectScoresSum[$aspect]['sum'] += $answer->likert_value;
                        $aspectScoresSum[$aspect]['count'] += 1;
                    }
                }
            }

            // Hitung nilai multiple choice (skala 0 - 100)
            $mcScore = 0.0;
            if ($multipleChoiceTotal > 0) {
                $mcScore = round(($multipleChoiceCorrect / $multipleChoiceTotal) * 100, 2);
            }

            // Hitung nilai rata-rata per aspek kepribadian (skala 0 - 100)
            $aspectScoresFormatted = [];
            foreach ($aspectScoresSum as $aspect => $data) {
                if ($data['count'] > 0) {
                    // Maksimal skor = count * 5
                    $aspectPercentage = round(($data['sum'] / ($data['count'] * 5)) * 100, 2);
                    $aspectScoresFormatted[$aspect] = [
                        'aspect' => $aspect,
                        'raw_sum' => $data['sum'],
                        'max_possible' => $data['count'] * 5,
                        'average' => round($data['sum'] / $data['count'], 2),
                        'score_100' => $aspectPercentage,
                    ];
                }
            }

            $passingGrade = $package->passing_grade ?? 70.00;
            $isPassed = $mcScore >= $passingGrade;

            $totalQuestions = count($attempt->question_order_json ?: []) ?: $package->total_questions;
            $totalUnanswered = max(0, $totalQuestions - $totalAnswered);

            $attempt->total_questions = $totalQuestions;
            $attempt->total_answered = $totalAnswered;
            $attempt->total_correct = $multipleChoiceCorrect;
            $attempt->total_wrong = $multipleChoiceWrong;
            $attempt->total_unanswered = $totalUnanswered;
            $attempt->score_multiple_choice = $mcScore;
            $attempt->aspect_scores_json = $aspectScoresFormatted;
            $attempt->is_passed = $isPassed;
            $attempt->status = 'completed';
            $attempt->submitted_at = $attempt->submitted_at ?? now();
            if ($attempt->started_at) {
                $attempt->duration_seconds_used = $attempt->started_at->diffInSeconds($attempt->submitted_at);
            }
            $attempt->save();

            // Update status & nilai kandidat
            $candidate = $attempt->candidate;
            if ($candidate) {
                $candidate->final_psychotest_score = $mcScore;
                if ($isPassed) {
                    $candidate->status = 'LULUS_PSIKOTES';
                } else {
                    $candidate->status = 'TIDAK_LULUS';
                    $candidate->final_status = 'TIDAK_LOLOS';
                }
                $candidate->save();
            }

            return $attempt;
        });
    }
}
