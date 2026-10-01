<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\TestAttempt;
use App\Models\TestPackage;
use App\Models\TestSchedule;
use App\Models\Vacancy;
use App\Services\AuditLogService;
use App\Services\TestEngineService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicTestController extends Controller
{
    public function __construct(
        protected TestEngineService $testEngineService
    ) {}

    /**
     * Public landing page displaying open job vacancies and recruitment positions.
     */
    public function index(): View
    {
        $vacancies = Vacancy::with(['position.testPackage.schedules' => function ($sq) {
            $sq->where('is_active', true)->orderBy('start_time', 'asc');
        }])
            ->where('status', 'open')
            ->latest()
            ->get()
            ->map(function ($vac) {
                // Cari paket psikotes untuk posisi lowongan ini
                $package = $vac->position?->testPackage
                    ?? TestPackage::where('is_active', true)->first();

                $schedules = $package ? $package->schedules->where('is_active', true) : collect();

                $activeSchedule = $schedules->first(function ($s) {
                    return now()->between($s->start_time, $s->end_time);
                });

                $upcomingSchedule = $schedules->filter(function ($s) {
                    return now()->isBefore($s->start_time);
                })->sortBy('start_time')->first();

                $latestSchedule = $schedules->sortByDesc('end_time')->first();

                $vac->testPackage = $package;
                $vac->activeSchedule = $activeSchedule;
                $vac->upcomingSchedule = $upcomingSchedule;
                $vac->latestSchedule = $latestSchedule;
                $vac->hasSchedule = $schedules->isNotEmpty();

                if (! $vac->hasSchedule) {
                    $vac->scheduleStatus = 'NO_SCHEDULE'; // Jadwal belum dibuat
                } elseif ($activeSchedule) {
                    $vac->scheduleStatus = 'ACTIVE'; // Sedang buka
                } elseif ($upcomingSchedule) {
                    $vac->scheduleStatus = 'UPCOMING'; // Belum mulai
                } else {
                    $vac->scheduleStatus = 'EXPIRED'; // Telah berakhir
                }

                return $vac;
            });

        return view('public.index', compact('vacancies'));
    }

    /**
     * Start test immediately from public entry form.
     */
    public function start(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vacancy_id' => ['required', 'exists:vacancies,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'gender' => ['required', 'in:L,P'],
            'nik' => ['nullable', 'string', 'max:30'],
            'education' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
        ]);

        $vacancy = Vacancy::with('position')->findOrFail($validated['vacancy_id']);

        // Cari paket untuk posisi ini
        $package = TestPackage::where('position_id', $vacancy->position_id)
            ->where('is_active', true)
            ->first()
            ?? TestPackage::where('is_active', true)->first();

        if (! $package) {
            return back()->with('error', 'Paket psikotes untuk posisi ini belum tersedia.');
        }

        // VALIDASI JADWAL TEST: Hanya bisa dibuka di saat jadwal test aktif saja!
        $activeSchedule = TestSchedule::where('test_package_id', $package->id)
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->first();

        if (! $activeSchedule) {
            $upcomingSchedule = TestSchedule::where('test_package_id', $package->id)
                ->where('is_active', true)
                ->where('start_time', '>', now())
                ->orderBy('start_time', 'asc')
                ->first();

            if ($upcomingSchedule) {
                return back()->with('error', "Ujian psikotes belum dapat dimulai. Jadwal tes baru dibuka pada {$upcomingSchedule->start_time->format('d M Y - H:i')} WIB.");
            }

            return back()->with('error', 'Ujian psikotes tidak dapat diakses karena jadwal tes belum dibuat oleh HRD atau telah berakhir.');
        }

        // Find or create candidate record based on email and vacancy
        $candidate = Candidate::where('email', $validated['email'])
            ->where('vacancy_id', $vacancy->id)
            ->first();

        if (! $candidate) {
            $candidate = Candidate::create([
                'vacancy_id' => $vacancy->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'access_token' => Str::random(40),
                'nik' => $validated['nik'] ?? null,
                'gender' => $validated['gender'],
                'education' => $validated['education'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => 'REGISTERED',
            ]);
        } else {
            // Update candidate details
            $candidate->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'nik' => $validated['nik'] ?? $candidate->nik,
                'gender' => $validated['gender'],
                'education' => $validated['education'] ?? $candidate->education,
            ]);
        }

        // Start or resume test attempt
        $attempt = $this->testEngineService->startOrResumeAttempt($candidate, $package);

        AuditLogService::log('PUBLIC_START_TEST', "Kandidat publik {$candidate->name} ({$candidate->email}) memulai psikotes {$package->name} pada sesi {$activeSchedule->name}");

        return redirect()->route('public.test.screen', ['token' => $candidate->access_token]);
    }

    /**
     * Show distraction-free public test screen.
     */
    public function showScreen(string $token): View|RedirectResponse
    {
        $candidate = Candidate::where('access_token', $token)->firstOrFail();

        $attempt = TestAttempt::where('candidate_id', $candidate->id)
            ->latest()
            ->firstOrFail();

        if ($attempt->status === 'completed' || now()->isAfter($attempt->server_end_time)) {
            $attempt = $this->testEngineService->submitAttempt($attempt);

            return redirect()->route('public.test.result', ['token' => $token]);
        }

        $attempt->load(['testPackage', 'answers.question.options', 'answers.question.category']);

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

        $remainingSeconds = max(0, (int) now()->diffInSeconds($attempt->server_end_time, false));
        $package = $attempt->testPackage;

        return view('public.test-screen', compact('candidate', 'attempt', 'package', 'questionsData', 'remainingSeconds', 'token'));
    }

    /**
     * AJAX Autosave during test.
     */
    public function autosave(Request $request, string $token): JsonResponse
    {
        $candidate = Candidate::where('access_token', $token)->firstOrFail();

        $attempt = TestAttempt::where('candidate_id', $candidate->id)
            ->where('status', 'in_progress')
            ->latest()
            ->firstOrFail();

        $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'option_id' => ['nullable', 'exists:question_options,id'],
            'likert_value' => ['nullable', 'integer', 'between:1,5'],
        ]);

        try {
            $this->testEngineService->saveAnswer(
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

    /**
     * Submit and finalize public test.
     */
    public function submitTest(Request $request, string $token): JsonResponse|RedirectResponse
    {
        $candidate = Candidate::where('access_token', $token)->firstOrFail();

        $attempt = TestAttempt::where('candidate_id', $candidate->id)
            ->latest()
            ->firstOrFail();

        $completedAttempt = $this->testEngineService->submitAttempt($attempt);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('public.test.result', ['token' => $token]),
            ]);
        }

        return redirect()->route('public.test.result', ['token' => $token]);
    }

    /**
     * Show post-test completion screen.
     */
    public function showResult(string $token): View
    {
        $candidate = Candidate::where('access_token', $token)->with('vacancy.position')->firstOrFail();

        $attempt = TestAttempt::where('candidate_id', $candidate->id)
            ->with('testPackage')
            ->latest()
            ->firstOrFail();

        $package = $attempt->testPackage;

        return view('public.test-result', compact('candidate', 'attempt', 'package', 'token'));
    }

    /**
     * Public check status by Email or Phone.
     */
    public function checkStatus(Request $request): View
    {
        $query = $request->query('q');
        $candidate = null;
        $testScheduleInfo = null;

        if ($query) {
            $candidate = Candidate::with([
                'vacancy.position.testPackage.schedules' => function ($sq) {
                    $sq->where('is_active', true)->orderBy('start_time', 'asc');
                },
                'latestAttempt.testPackage',
                'latestInterview.interviewer',
            ])
                ->where('email', $query)
                ->orWhere('phone', $query)
                ->latest()
                ->first();

            if ($candidate) {
                $package = $candidate->vacancy?->position?->testPackage
                    ?? TestPackage::where('is_active', true)->first();

                $schedules = $package ? $package->schedules->where('is_active', true) : collect();

                $activeSchedule = $schedules->first(function ($s) {
                    return now()->between($s->start_time, $s->end_time);
                });

                $upcomingSchedule = $schedules->filter(function ($s) {
                    return now()->isBefore($s->start_time);
                })->sortBy('start_time')->first();

                $latestSchedule = $schedules->sortByDesc('end_time')->first();

                $testScheduleInfo = [
                    'package' => $package,
                    'has_schedule' => $schedules->isNotEmpty(),
                    'active_schedule' => $activeSchedule,
                    'upcoming_schedule' => $upcomingSchedule,
                    'latest_schedule' => $latestSchedule,
                    'status' => ! $schedules->isNotEmpty()
                        ? 'NO_SCHEDULE'
                        : ($activeSchedule ? 'ACTIVE' : ($upcomingSchedule ? 'UPCOMING' : 'EXPIRED')),
                ];
            }
        }

        return view('public.check-status', compact('candidate', 'query', 'testScheduleInfo'));
    }
}
