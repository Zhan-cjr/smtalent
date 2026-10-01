<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Vacancy;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VacancyController extends Controller
{
    public function index(): View
    {
        $vacancies = Vacancy::with('position')->withCount('candidates')->latest()->get();
        $positions = Position::where('is_active', true)->get();

        return view('hrd.vacancies.index', compact('vacancies', 'positions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'position_id' => ['required', 'exists:positions,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quota' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,open,closed'],
        ]);

        $vac = Vacancy::create($validated);
        AuditLogService::log('CREATE_VACANCY', "Membuat lowongan baru: {$vac->title}");

        return redirect()->route('hrd.vacancies.index')->with('success', 'Lowongan pekerjaan berhasil ditambahkan.');
    }

    public function update(Request $request, Vacancy $vacancy): RedirectResponse
    {
        $validated = $request->validate([
            'position_id' => ['required', 'exists:positions,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quota' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,open,closed'],
        ]);

        $vacancy->update($validated);
        AuditLogService::log('UPDATE_VACANCY', "Mengubah lowongan: {$vacancy->title}");

        return redirect()->route('hrd.vacancies.index')->with('success', 'Lowongan pekerjaan berhasil diperbarui.');
    }

    public function destroy(Vacancy $vacancy): RedirectResponse
    {
        $title = $vacancy->title;
        $vacancy->delete();
        AuditLogService::log('DELETE_VACANCY', "Menghapus lowongan: {$title}");

        return redirect()->route('hrd.vacancies.index')->with('success', 'Lowongan pekerjaan berhasil dihapus.');
    }
}
