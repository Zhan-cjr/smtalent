<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Position;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalCandidates = Candidate::count();
        $countRegistered = Candidate::where('status', 'REGISTERED')->count();
        $countTesting = Candidate::where('status', 'PSIKOTES')->count();
        $countPassedPsychotest = Candidate::whereIn('status', ['LULUS_PSIKOTES', 'INTERVIEW', 'LULUS_INTERVIEW', 'DITERIMA'])->count();
        $countInterview = Candidate::whereIn('status', ['INTERVIEW', 'LULUS_INTERVIEW'])->count();
        $countLolos = Candidate::where('final_status', 'LOLOS')->orWhere('status', 'DITERIMA')->count();
        $countCadangan = Candidate::where('final_status', 'CADANGAN')->orWhere('status', 'CADANGAN')->count();
        $countTidakLolos = Candidate::where('final_status', 'TIDAK_LOLOS')->orWhere('status', 'TIDAK_LULUS')->count();

        // Data Chart: Kandidat per Posisi
        $candidatesPerPosition = Position::withCount(['vacancies as candidates_count' => function ($query) {
            $query->join('candidates', 'vacancies.id', '=', 'candidates.vacancy_id');
        }])->get();

        $positionLabels = $candidatesPerPosition->pluck('name')->toArray();
        $positionData = $candidatesPerPosition->pluck('candidates_count')->toArray();

        // Data Chart: Distribusi Nilai Psikotes
        $scoreRanges = [
            '< 50' => Candidate::whereNotNull('final_psychotest_score')->where('final_psychotest_score', '<', 50)->count(),
            '50 - 69' => Candidate::whereNotNull('final_psychotest_score')->whereBetween('final_psychotest_score', [50, 69.99])->count(),
            '70 - 85' => Candidate::whereNotNull('final_psychotest_score')->whereBetween('final_psychotest_score', [70, 85.99])->count(),
            '86 - 100' => Candidate::whereNotNull('final_psychotest_score')->whereBetween('final_psychotest_score', [86, 100])->count(),
        ];

        $recentLogs = AuditLog::with('user')->latest('created_at')->take(8)->get();
        $recentCandidates = Candidate::with(['user', 'vacancy.position'])->latest()->take(6)->get();

        return view('hrd.dashboard', compact(
            'totalCandidates',
            'countRegistered',
            'countTesting',
            'countPassedPsychotest',
            'countInterview',
            'countLolos',
            'countCadangan',
            'countTidakLolos',
            'positionLabels',
            'positionData',
            'scoreRanges',
            'recentLogs',
            'recentCandidates'
        ));
    }
}
