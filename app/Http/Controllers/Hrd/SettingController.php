<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditLogService;
use App\Services\FinalScoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected FinalScoreService $finalScoreService
    ) {}

    public function index(): View
    {
        $settings = Setting::all()->pluck('value', 'key');

        return view('hrd.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'psychotest_weight' => ['required', 'numeric', 'min:0', 'max:100'],
            'interview_weight' => ['required', 'numeric', 'min:0', 'max:100'],
            'default_passing_grade' => ['required', 'numeric', 'min:0', 'max:100'],
            'default_duration_minutes' => ['required', 'integer', 'min:5'],
            'company_name' => ['required', 'string', 'max:255'],
        ]);

        if (floatval($request->psychotest_weight) + floatval($request->interview_weight) != 100) {
            return back()->withErrors(['interview_weight' => 'Total Bobot Psikotes dan Interview harus pas 100%.'])->withInput();
        }

        Setting::set('psychotest_weight', $request->psychotest_weight, 'Bobot persentase psikotes (%)');
        Setting::set('interview_weight', $request->interview_weight, 'Bobot persentase interview (%)');
        Setting::set('default_passing_grade', $request->default_passing_grade, 'Passing grade kelulusan psikotes');
        Setting::set('default_duration_minutes', $request->default_duration_minutes, 'Durasi standar ujian');
        Setting::set('company_name', $request->company_name, 'Nama instansi/perusahaan');

        // Recalculate candidate final scores with new weights
        $this->finalScoreService->recalculateAllFinalScores();

        AuditLogService::log('UPDATE_SETTINGS', "Mengubah konfigurasi bobot penilaian (Psikotes: {$request->psychotest_weight}%, Interview: {$request->interview_weight}%)");

        return back()->with('success', 'Pengaturan sistem & bobot penilaian berhasil diperbarui. Seluruh ranking akhir telah dihitung ulang.');
    }
}
