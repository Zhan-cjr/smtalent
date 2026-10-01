<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CandidateProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();
        $candidate = Candidate::with('vacancy.position')->where('user_id', $user->id)->firstOrFail();

        return view('candidate.profile', compact('user', 'candidate'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:25'],
            'nik' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:L,P'],
            'birth_date' => ['nullable', 'date'],
            'education' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        $candidate->update([
            'nik' => $request->nik,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'education' => $request->education,
            'address' => $request->address,
        ]);

        AuditLogService::log('UPDATE_PROFILE', "Kandidat {$user->name} memperbarui data profil.");

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
