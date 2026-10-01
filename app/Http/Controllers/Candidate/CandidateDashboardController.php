<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\TestPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CandidateDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $candidate = Candidate::with([
            'vacancy.position',
            'latestAttempt.testPackage',
            'latestInterview.interviewer',
        ])->where('user_id', $user->id)->firstOrFail();

        // Cari paket tes yang sesuai untuk lowongan / posisi ini
        $positionId = $candidate->vacancy?->position_id;
        $testPackage = TestPackage::where('position_id', $positionId)
            ->where('is_active', true)
            ->first()
            ?? TestPackage::where('is_active', true)->first();

        $attempt = $candidate->latestAttempt;

        return view('candidate.dashboard', compact('candidate', 'testPackage', 'attempt'));
    }
}
