<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Position;
use App\Models\TestAttempt;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\InterviewScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InterviewController extends Controller
{
    public function __construct(
        protected InterviewScoringService $interviewScoringService
    ) {}

    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'candidates');
        $search = $request->query('search');
        $positionId = $request->query('position_id');
        $interviewStatus = $request->query('status');

        // =========================================================================
        // 1. Antrean Kandidat Siap Interview
        // URUTAN: Yang pertama selesai mengerjakan test psikotes paling atas/awal!
        // =========================================================================
        $candidateQuery = Candidate::query()
            ->with([
                'user',
                'vacancy.position',
                'activeInterview.interviewer',
                'latestInterview.interviewer',
                'latestAttempt',
            ])
            ->where(function ($q) {
                $q->whereHas('testAttempts', function ($aq) {
                    $aq->where('status', 'completed');
                })->orWhereIn('status', ['LULUS_PSIKOTES', 'INTERVIEW', 'LULUS_INTERVIEW', 'DITERIMA', 'CADANGAN']);
            })
            ->addSelect([
                'candidates.*',
                'first_submitted_at' => TestAttempt::select('submitted_at')
                    ->whereColumn('test_attempts.candidate_id', 'candidates.id')
                    ->where('test_attempts.status', 'completed')
                    ->orderBy('submitted_at', 'asc')
                    ->limit(1),
            ]);

        if ($search) {
            $candidateQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhereHas('vacancy.position', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($positionId) {
            $candidateQuery->whereHas('vacancy', function ($vq) use ($positionId) {
                $vq->where('position_id', $positionId);
            });
        }

        // Urutan: Yang pertama selesai tes psikotes paling atas / awal
        $candidates = $candidateQuery
            ->orderByRaw('COALESCE(first_submitted_at, candidates.updated_at) ASC')
            ->paginate(15, ['*'], 'candidate_page')
            ->withQueryString();

        // =========================================================================
        // 2. Daftar Sesi Wawancara Terjadwal / Riwayat
        // =========================================================================
        $interviewQuery = Interview::with(['candidate.user', 'candidate.vacancy.position', 'interviewer', 'scores']);

        if ($interviewStatus) {
            $interviewQuery->where('status', $interviewStatus);
        }

        if ($search) {
            $interviewQuery->whereHas('candidate', function ($cq) use ($search) {
                $cq->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $interviews = $interviewQuery->latest('scheduled_at')
            ->paginate(15, ['*'], 'interview_page')
            ->withQueryString();

        $positions = Position::where('is_active', true)->get();
        $interviewers = User::where('role', 'hrd')->where('is_active', true)->get();

        return view('hrd.interviews.index', compact(
            'candidates',
            'interviews',
            'positions',
            'interviewers',
            'tab',
            'search',
            'positionId',
            'interviewStatus'
        ));
    }

    /**
     * HRD langsung memilih kandidat untuk mulai interview.
     * Mengunci kandidat ke HRD ini agar HRD lain tidak bisa memilih kandidat yang sama.
     */
    public function claim(Request $request): RedirectResponse
    {
        $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
        ]);

        $candidate = Candidate::with('activeInterview.interviewer')->findOrFail($request->candidate_id);

        // Periksa apakah kandidat sedang diwawancarai oleh HRD lain
        if ($candidate->activeInterview) {
            if ($candidate->activeInterview->interviewer_id !== auth()->id()) {
                $interviewerName = $candidate->activeInterview->interviewer?->name ?? 'HRD lain';

                return back()->with('error', "Kandidat {$candidate->name} saat ini sedang dipilih / di-interview oleh {$interviewerName}. Anda tidak dapat memilih kandidat tersebut.");
            }

            // Jika sedang diwawancarai oleh HRD saat ini, langsung arahkan ke form nilai
            return redirect()->route('hrd.interviews.score', $candidate->activeInterview->id);
        }

        // Buat sesi interview baru untuk HRD saat ini
        $interview = Interview::create([
            'candidate_id' => $candidate->id,
            'interviewer_id' => auth()->id(),
            'scheduled_at' => now(),
            'location' => 'Sesi Langsung ('.auth()->user()->name.')',
            'notes' => 'Wawancara langsung dimulai oleh '.auth()->user()->name,
            'status' => 'in_progress',
        ]);

        $candidate->update(['status' => 'INTERVIEW']);

        AuditLogService::log(
            'CLAIM_INTERVIEW',
            'HRD '.auth()->user()->name." memilih kandidat {$candidate->name} untuk sesi interview."
        );

        return redirect()->route('hrd.interviews.score', $interview->id)
            ->with('success', "Kandidat {$candidate->name} berhasil Anda pilih. Sesi interview terkunci untuk Anda.");
    }

    /**
     * Lepas kandidat yang sedang dipilih agar HRD lain dapat memilihnya kembali.
     */
    public function release(Interview $interview): RedirectResponse
    {
        if ($interview->isLockedByOther()) {
            return back()->with('error', "Anda tidak berhak melepas wawancara yang sedang dilakukan oleh {$interview->interviewer?->name}.");
        }

        if ($interview->status === 'completed') {
            return back()->with('error', 'Wawancara yang sudah selesai dinilai tidak dapat dilepas.');
        }

        $candidate = $interview->candidate;
        $interview->delete();

        if ($candidate) {
            // Kembalikan status kandidat jika belum selesai
            $candidate->update(['status' => 'LULUS_PSIKOTES']);
        }

        AuditLogService::log(
            'RELEASE_INTERVIEW',
            'HRD '.auth()->user()->name." membatalkan/melepas pilihan kandidat {$candidate?->name}."
        );

        return back()->with('success', "Kandidat {$candidate?->name} telah dilepas dari antrean Anda dan siap dipilih oleh HRD lain.");
    }

    public function create(Request $request): View|RedirectResponse
    {
        $candidateId = $request->query('candidate_id');
        $candidate = null;
        if ($candidateId) {
            $candidate = Candidate::with('user', 'vacancy.position')->findOrFail($candidateId);
        }

        $eligibleCandidates = Candidate::whereIn('status', ['LULUS_PSIKOTES', 'INTERVIEW'])
            ->with(['user', 'vacancy.position', 'activeInterview.interviewer'])
            ->get();

        $interviewers = User::where('role', 'hrd')->where('is_active', true)->get();

        return view('hrd.interviews.create', compact('candidate', 'eligibleCandidates', 'interviewers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
            'interviewer_id' => ['required', 'exists:users,id'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $candidate = Candidate::with('activeInterview.interviewer')->findOrFail($request->candidate_id);

        // Periksa apakah kandidat sudah dikunci oleh HRD lain
        if ($candidate->activeInterview && $candidate->activeInterview->interviewer_id !== (int) $request->interviewer_id) {
            $interviewerName = $candidate->activeInterview->interviewer?->name ?? 'HRD lain';

            return back()->with('error', "Kandidat {$candidate->name} sedang diwawancarai oleh {$interviewerName}. Tidak dapat dijadwalkan ulang.");
        }

        $interview = Interview::create([
            'candidate_id' => $candidate->id,
            'interviewer_id' => $request->interviewer_id,
            'scheduled_at' => $request->scheduled_at,
            'location' => $request->location,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        $candidate->status = 'INTERVIEW';
        $candidate->save();

        AuditLogService::log('SCHEDULE_INTERVIEW', "Menjadwalkan interview untuk kandidat {$candidate->name} pada {$request->scheduled_at}");

        return redirect()->route('hrd.interviews.index')->with('success', 'Jadwal interview berhasil dibuat.');
    }

    public function showScoreForm(Interview $interview): View|RedirectResponse
    {
        // Proteksi: HRD lain tidak boleh mengisi nilai jika interview milik HRD berbeda
        if ($interview->isLockedByOther()) {
            return redirect()->route('hrd.interviews.index')
                ->with('error', "Kandidat ini sedang di-interview oleh {$interview->interviewer?->name}. Anda tidak dapat mengakses lembar penilaian ini.");
        }

        $interview->load(['candidate.user', 'candidate.vacancy.position', 'interviewer', 'scores']);

        $aspectNames = [
            'Komunikasi',
            'Sikap',
            'Motivasi',
            'Pengalaman',
            'Pengetahuan',
            'Problem Solving',
            'Kerja Sama',
            'Kedisiplinan',
        ];

        return view('hrd.interviews.score-form', compact('interview', 'aspectNames'));
    }

    public function submitScore(Request $request, Interview $interview): RedirectResponse
    {
        if ($interview->isLockedByOther()) {
            return redirect()->route('hrd.interviews.index')
                ->with('error', "Kandidat ini sedang di-interview oleh {$interview->interviewer?->name}. Anda tidak memiliki izin menginput nilai.");
        }

        $request->validate([
            'aspects' => ['required', 'array'],
            'aspects.*' => ['required', 'numeric', 'min:0', 'max:100'],
            'strengths' => ['nullable', 'string'],
            'weaknesses' => ['nullable', 'string'],
            'recommendation' => ['required', 'in:DISARANKAN,DIPERTIMBANGKAN,TIDAK_DISARANKAN'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->interviewScoringService->saveInterviewScore(
            $interview,
            $request->aspects,
            [
                'notes' => $request->notes,
                'strengths' => $request->strengths,
                'weaknesses' => $request->weaknesses,
                'recommendation' => $request->recommendation,
            ]
        );

        AuditLogService::log('INPUT_INTERVIEW_SCORE', "Menginput skor interview untuk {$interview->candidate->name} dengan rata-rata {$interview->average_score}");

        return redirect()->route('hrd.interviews.index')->with('success', 'Penilaian interview berhasil disimpan & nilai akhir telah dikalkulasi.');
    }
}
