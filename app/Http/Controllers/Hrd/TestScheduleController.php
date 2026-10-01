<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\TestPackage;
use App\Models\TestSchedule;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestScheduleController extends Controller
{
    public function index(): View
    {
        $schedules = TestSchedule::with('testPackage.position')
            ->latest('start_time')
            ->paginate(15);

        $packages = TestPackage::with('position')
            ->where('is_active', true)
            ->get();

        return view('hrd.test-schedules.index', compact('schedules', 'packages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_package_id' => ['required', 'exists:test_packages,id'],
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $schedule = TestSchedule::create($validated);

        AuditLogService::log(
            'CREATE_TEST_SCHEDULE',
            "Membuat Jadwal Psikotes: {$schedule->name} untuk paket {$schedule->testPackage?->name} ({$schedule->start_time} - {$schedule->end_time})"
        );

        return redirect()->route('hrd.test-schedules.index')->with('success', 'Jadwal tes psikotes berhasil dibuat.');
    }

    public function update(Request $request, TestSchedule $testSchedule): RedirectResponse
    {
        $validated = $request->validate([
            'test_package_id' => ['required', 'exists:test_packages,id'],
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $testSchedule->update($validated);

        AuditLogService::log(
            'UPDATE_TEST_SCHEDULE',
            "Memperbarui Jadwal Psikotes: {$testSchedule->name} ({$testSchedule->start_time} - {$testSchedule->end_time})"
        );

        return redirect()->route('hrd.test-schedules.index')->with('success', 'Jadwal tes psikotes berhasil diperbarui.');
    }

    public function destroy(TestSchedule $testSchedule): RedirectResponse
    {
        $name = $testSchedule->name;
        $testSchedule->delete();

        AuditLogService::log('DELETE_TEST_SCHEDULE', "Menghapus Jadwal Psikotes: {$name}");

        return redirect()->route('hrd.test-schedules.index')->with('success', 'Jadwal tes psikotes berhasil dihapus.');
    }
}
