<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Setting;
use App\Models\Vacancy;
use App\Services\AuditLogService;
use App\Services\RankingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function __construct(
        protected RankingService $rankingService
    ) {}

    public function psychotestRankings(Request $request): View
    {
        $vacancyId = $request->query('vacancy_id');
        $positionId = $request->query('position_id');
        $status = $request->query('status');

        $rankings = $this->rankingService->getPsychotestRankings(
            $vacancyId ? intval($vacancyId) : null,
            $positionId ? intval($positionId) : null,
            $status
        );

        $vacancies = Vacancy::where('status', 'open')->get();
        $positions = Position::where('is_active', true)->get();
        $passingGrade = Setting::get('default_passing_grade', 70);

        return view('hrd.rankings.psychotest', compact('rankings', 'vacancies', 'positions', 'vacancyId', 'positionId', 'status', 'passingGrade'));
    }

    public function finalRankings(Request $request): View
    {
        $vacancyId = $request->query('vacancy_id');
        $positionId = $request->query('position_id');
        $finalStatus = $request->query('final_status');

        $rankings = $this->rankingService->getFinalRankings(
            $vacancyId ? intval($vacancyId) : null,
            $positionId ? intval($positionId) : null,
            $finalStatus
        );

        $vacancies = Vacancy::where('status', 'open')->get();
        $positions = Position::where('is_active', true)->get();
        $psychotestWeight = Setting::get('psychotest_weight', 60);
        $interviewWeight = Setting::get('interview_weight', 40);

        return view('hrd.rankings.final', compact('rankings', 'vacancies', 'positions', 'vacancyId', 'positionId', 'finalStatus', 'psychotestWeight', 'interviewWeight'));
    }

    public function updateFinalDecision(Request $request, Candidate $candidate): RedirectResponse
    {
        $request->validate([
            'final_status' => ['required', 'in:LOLOS,CADANGAN,TIDAK_LOLOS'],
        ]);

        $candidate->final_status = $request->final_status;
        if ($request->final_status === 'LOLOS') {
            $candidate->status = 'DITERIMA';
        } elseif ($request->final_status === 'CADANGAN') {
            $candidate->status = 'CADANGAN';
        } else {
            $candidate->status = 'TIDAK_LULUS';
        }
        $candidate->save();

        AuditLogService::log('FINAL_DECISION', "Menetapkan keputusan akhir untuk {$candidate->name} sebagai: {$request->final_status}");

        return back()->with('success', "Keputusan status untuk kandidat {$candidate->name} berhasil disimpan.");
    }
}
