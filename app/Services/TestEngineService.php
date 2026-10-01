<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Question;
use App\Models\TestAnswer;
use App\Models\TestAttempt;
use App\Models\TestPackage;
use Exception;
use Illuminate\Support\Facades\DB;

class TestEngineService
{
    public function __construct(
        protected PsychotestScoringService $scoringService
    ) {}

    /**
     * Start or resume a test attempt for a candidate.
     */
    public function startOrResumeAttempt(Candidate $candidate, TestPackage $package): TestAttempt
    {
        // Cari attempt yang sedang berjalan (in_progress)
        $existingAttempt = TestAttempt::where('candidate_id', $candidate->id)
            ->where('test_package_id', $package->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            // Periksa apakah waktu server sudah habis
            if (now()->isAfter($existingAttempt->server_end_time)) {
                $existingAttempt->status = 'completed';
                $existingAttempt->submitted_at = $existingAttempt->server_end_time;
                $existingAttempt->save();

                return $this->scoringService->scoreAttempt($existingAttempt);
            }

            return $existingAttempt;
        }

        // Cek jika sudah pernah selesai
        $completedAttempt = TestAttempt::where('candidate_id', $candidate->id)
            ->where('test_package_id', $package->id)
            ->where('status', 'completed')
            ->first();

        if ($completedAttempt) {
            return $completedAttempt;
        }

        // Buat attempt baru
        return DB::transaction(function () use ($candidate, $package) {
            $durationMinutes = $package->duration_minutes ?: 50;
            $now = now();
            $serverEndTime = (clone $now)->addMinutes($durationMinutes);

            // Ambil pertanyaan dari paket
            $packageQuestions = $package->questions()->where('questions.is_active', true)->get();

            // Jika paket belum di-assign spesifik, ambil soal umum + soal posisi
            if ($packageQuestions->isEmpty()) {
                $positionId = $candidate->vacancy?->position_id;
                $generalQuestions = Question::whereNull('position_id')->where('is_active', true)->get();
                $positionQuestions = Question::where('position_id', $positionId)->where('is_active', true)->get();
                $allPool = $generalQuestions->merge($positionQuestions);

                if ($package->is_randomized) {
                    $selectedQuestions = $allPool->shuffle()->take($package->total_questions ?: 40);
                } else {
                    $selectedQuestions = $allPool->take($package->total_questions ?: 40);
                }
            } else {
                if ($package->is_randomized) {
                    $selectedQuestions = $packageQuestions->shuffle()->take($package->total_questions ?: 40);
                } else {
                    $selectedQuestions = $packageQuestions->take($package->total_questions ?: 40);
                }
            }

            $questionIds = $selectedQuestions->pluck('id')->values()->toArray();

            $attempt = TestAttempt::create([
                'candidate_id' => $candidate->id,
                'test_package_id' => $package->id,
                'started_at' => $now,
                'server_end_time' => $serverEndTime,
                'total_questions' => count($questionIds),
                'total_answered' => 0,
                'total_unanswered' => count($questionIds),
                'status' => 'in_progress',
                'question_order_json' => $questionIds,
            ]);

            // Inisialisasi baris jawaban kosong untuk setiap soal
            foreach ($questionIds as $qId) {
                TestAnswer::create([
                    'test_attempt_id' => $attempt->id,
                    'question_id' => $qId,
                ]);
            }

            // Update candidate status to PSIKOTES
            $candidate->status = 'PSIKOTES';
            $candidate->save();

            AuditLogService::log('START_TEST', "Kandidat {$candidate->name} memulai psikotes paket {$package->name}");

            return $attempt;
        });
    }

    /**
     * Autosave an answer during test.
     */
    public function saveAnswer(TestAttempt $attempt, int $questionId, ?int $optionId, ?int $likertValue): TestAnswer
    {
        // Validasi status attempt
        if ($attempt->status !== 'in_progress' || now()->isAfter($attempt->server_end_time)) {
            throw new Exception('Waktu pengerjaan tes telah berakhir atau tes sudah diselesaikan.');
        }

        $answer = TestAnswer::where('test_attempt_id', $attempt->id)
            ->where('question_id', $questionId)
            ->firstOrFail();

        $question = Question::findOrFail($questionId);

        if ($question->type === 'multiple_choice') {
            $answer->selected_option_id = $optionId;
            $answer->likert_value = null;
        } elseif ($question->type === 'likert_scale') {
            $answer->likert_value = $likertValue;
            $answer->selected_option_id = null;
        }

        $answer->answered_at = now();
        $answer->save();

        // Update hitungan total_answered pada attempt
        $totalAnswered = TestAnswer::where('test_attempt_id', $attempt->id)
            ->where(function ($q) {
                $q->whereNotNull('selected_option_id')
                    ->orWhereNotNull('likert_value');
            })->count();

        $attempt->total_answered = $totalAnswered;
        $attempt->total_unanswered = max(0, $attempt->total_questions - $totalAnswered);
        $attempt->save();

        return $answer;
    }

    /**
     * Submit and finish attempt.
     */
    public function submitAttempt(TestAttempt $attempt): TestAttempt
    {
        if ($attempt->status === 'completed') {
            return $attempt;
        }

        $attempt->status = 'completed';
        $attempt->submitted_at = now();
        if ($attempt->started_at) {
            $attempt->duration_seconds_used = $attempt->started_at->diffInSeconds($attempt->submitted_at);
        }
        $attempt->save();

        $result = $this->scoringService->scoreAttempt($attempt);

        AuditLogService::log('SUBMIT_TEST', "Kandidat {$attempt->candidate->name} menyelesaikan psikotes dengan skor {$result->score_multiple_choice}");

        return $result;
    }
}
