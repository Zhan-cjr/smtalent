<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\TestAttempt;
use App\Models\TestPackage;
use App\Services\TestEngineService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CandidateTestController extends Controller
{
    public function __construct(
        protected TestEngineService $testEngineService
    ) {}

    public function showIntro(): View|RedirectResponse
    {
        $user = Auth::user();
        $candidate = Candidate::with('vacancy.position')->where('user_id', $user->id)->firstOrFail();

        $positionId = $candidate->vacancy?->position_id;
        $package = TestPackage::where('position_id', $positionId)->where('is_active', true)->first()
            ?? TestPackage::where('is_active', true)->firstOrFail();

        // Cek jika sudah pernah mengerjakan
        $existingAttempt = TestAttempt::where('candidate_id', $candidate->id)
            ->where('test_package_id', $package->id)
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->status === 'completed') {
                return redirect()->route('candidate.test.result', $existingAttempt->id);
            }
            if ($existingAttempt->status === 'in_progress') {
                return redirect()->route('candidate.test.screen', $existingAttempt->id);
            }
        }

        return view('candidate.test-intro', compact('candidate', 'package'));
    }

    public function startTest(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $candidate = Candidate::with('vacancy.position')->where('user_id', $user->id)->firstOrFail();

        $positionId = $candidate->vacancy?->position_id;
        $package = TestPackage::where('position_id', $positionId)->where('is_active', true)->first()
            ?? TestPackage::where('is_active', true)->firstOrFail();

        $attempt = $this->testEngineService->startOrResumeAttempt($candidate, $package);

        if ($attempt->status === 'completed') {
            return redirect()->route('candidate.test.result', $attempt->id);
        }

        return redirect()->route('candidate.test.screen', $attempt->id);
    }

    public function showScreen(TestAttempt $attempt): View|RedirectResponse
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        if ($attempt->candidate_id !== $candidate->id) {
            abort(403, 'Akses tidak sah ke sesi ujian kandidat lain.');
        }

        if ($attempt->status === 'completed' || now()->isAfter($attempt->server_end_time)) {
            $attempt = $this->testEngineService->submitAttempt($attempt);

            return redirect()->route('candidate.test.result', $attempt->id);
        }

        $attempt->load(['testPackage', 'answers.question.options', 'answers.question.category']);

        // Urutkan soal sesuai question_order_json yang tersimpan di database
        $orderedQuestionIds = $attempt->question_order_json ?: [];
        $answersMap = $attempt->answers->keyBy('question_id');

        $questionsData = [];
        $index = 1;
        foreach ($orderedQuestionIds as $qId) {
            $ans = $answersMap->get($qId);
            if ($ans && $ans->question) {
                $q = $ans->question;
                $options = $q->options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'key' => $opt->option_key,
                        'text' => $opt->option_text,
                    ];
                });

                if ($attempt->testPackage->is_options_randomized && $q->type === 'multiple_choice') {
                    $options = $options->shuffle();
                }

                $questionsData[] = [
                    'number' => $index++,
                    'question_id' => $q->id,
                    'type' => $q->type,
                    'aspect' => $q->aspect,
                    'category' => $q->category?->name,
                    'text' => $q->question_text,
                    'options' => $options,
                    'selected_option_id' => $ans->selected_option_id,
                    'likert_value' => $ans->likert_value,
                    'is_answered' => ($ans->selected_option_id !== null || $ans->likert_value !== null),
                ];
            }
        }

        $remainingSeconds = max(0, $attempt->server_end_time->diffInSeconds(now()));
        $package = $attempt->testPackage;

        return view('candidate.test-ongoing', compact('attempt', 'candidate', 'package', 'questionsData', 'remainingSeconds'));
    }

    public function autosave(Request $request, TestAttempt $attempt): JsonResponse
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        if ($attempt->candidate_id !== $candidate->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'option_id' => ['nullable', 'exists:question_options,id'],
            'likert_value' => ['nullable', 'integer', 'between:1,5'],
        ]);

        try {
            $answer = $this->testEngineService->saveAnswer(
                $attempt,
                $request->question_id,
                $request->option_id,
                $request->likert_value
            );

            return response()->json([
                'success' => true,
                'total_answered' => $attempt->total_answered,
                'total_unanswered' => $attempt->total_unanswered,
                'message' => 'Jawaban tersimpan otomatis.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function submitTest(Request $request, TestAttempt $attempt): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        if ($attempt->candidate_id !== $candidate->id) {
            abort(403, 'Unauthorized');
        }

        $completedAttempt = $this->testEngineService->submitAttempt($attempt);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('candidate.test.result', $completedAttempt->id),
            ]);
        }

        return redirect()->route('candidate.test.result', $completedAttempt->id)
            ->with('success', 'Psikotes telah berhasil disubmit.');
    }

    public function showResult(TestAttempt $attempt): View
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        if ($attempt->candidate_id !== $candidate->id) {
            abort(403, 'Unauthorized');
        }

        $attempt->load(['testPackage', 'candidate.vacancy.position']);
        $package = $attempt->testPackage;

        return view('candidate.test-result', compact('attempt', 'candidate', 'package'));
    }
}
