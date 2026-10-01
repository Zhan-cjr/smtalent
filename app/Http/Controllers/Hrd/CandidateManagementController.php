<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Vacancy;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CandidateManagementController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $vacancyId = $request->query('vacancy_id');
        $positionId = $request->query('position_id');
        $search = $request->query('search');

        $query = Candidate::with(['user', 'vacancy.position', 'latestAttempt', 'latestInterview']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($vacancyId) {
            $query->where('vacancy_id', $vacancyId);
        }

        if ($positionId) {
            $query->whereHas('vacancy', function ($q) use ($positionId) {
                $q->where('position_id', $positionId);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('nik', 'like', '%'.$search.'%');
            });
        }

        $candidates = $query->latest()->paginate(15)->withQueryString();
        $positions = Position::where('is_active', true)->get();
        $vacancies = Vacancy::where('status', 'open')->get();

        return view('hrd.candidates.index', compact('candidates', 'positions', 'vacancies', 'status', 'vacancyId', 'positionId', 'search'));
    }

    public function show(Candidate $candidate): View
    {
        $candidate->load([
            'user',
            'vacancy.position',
            'testAttempts.testPackage',
            'testAttempts.answers.question.category',
            'testAttempts.answers.selectedOption',
            'interviews.interviewer',
            'interviews.scores',
        ]);

        $latestAttempt = $candidate->testAttempts->last();
        $latestInterview = $candidate->interviews->last();

        return view('hrd.candidates.show', compact('candidate', 'latestAttempt', 'latestInterview'));
    }

    public function updateStatus(Request $request, Candidate $candidate): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:REGISTERED,PSIKOTES,LULUS_PSIKOTES,INTERVIEW,LULUS_INTERVIEW,CADANGAN,TIDAK_LULUS,DITERIMA'],
            'final_status' => ['nullable', 'in:LOLOS,CADANGAN,TIDAK_LOLOS'],
        ]);

        $oldStatus = $candidate->status;
        $candidate->status = $request->status;
        if ($request->has('final_status')) {
            $candidate->final_status = $request->final_status;
        }
        $candidate->save();

        AuditLogService::log('UPDATE_STATUS', "Mengubah status kandidat {$candidate->name} dari {$oldStatus} ke {$candidate->status}");

        return back()->with('success', "Status kandidat berhasil diperbarui menjadi {$candidate->status}.");
    }
}
