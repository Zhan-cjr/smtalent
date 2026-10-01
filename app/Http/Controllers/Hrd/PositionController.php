<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(): View
    {
        $positions = Position::withCount(['vacancies', 'questions'])->get();

        return view('hrd.positions.index', compact('positions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:positions,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $pos = Position::create($validated);
        AuditLogService::log('CREATE_POSITION', "Menambah posisi baru: {$pos->name} ({$pos->code})");

        return redirect()->route('hrd.positions.index')->with('success', 'Posisi jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:positions,code,'.$position->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $position->update($validated);
        AuditLogService::log('UPDATE_POSITION', "Mengubah posisi: {$position->name} ({$position->code})");

        return redirect()->route('hrd.positions.index')->with('success', 'Posisi jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        $name = $position->name;
        $position->delete();
        AuditLogService::log('DELETE_POSITION', "Menghapus posisi: {$name}");

        return redirect()->route('hrd.positions.index')->with('success', 'Posisi jabatan berhasil dihapus.');
    }
}
