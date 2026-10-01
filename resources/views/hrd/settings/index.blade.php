@extends('layouts.hrd', [
    'title' => 'Pengaturan & Bobot Seleksi',
    'headerTitle' => 'Pengaturan Sistem & Bobot Nilai',
    'headerSubtitle' => 'Konfigurasi persentase penilaian akhir (Psikotes + Interview), passing grade, dan durasi tes'
])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <form action="{{ route('hrd.settings.update') }}" method="POST" class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        @csrf

        <div>
            <h3 class="text-base font-bold text-slate-900">Formula Pembobotan Nilai Akhir</h3>
            <p class="text-xs text-slate-500">Total persentase bobot psikotes dan wawancara harus berjumlah 100%</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Bobot Nilai Psikotes (%)</label>
                <input type="number" name="psychotest_weight" value="{{ old('psychotest_weight', $settings['psychotest_weight'] ?? 60) }}" min="0" max="100" required
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 focus:border-indigo-500 text-sm font-bold text-indigo-600">
                <span class="text-[11px] text-slate-500 block mt-1">Default standar rekrutmen: 60%</span>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Bobot Nilai Interview (%)</label>
                <input type="number" name="interview_weight" value="{{ old('interview_weight', $settings['interview_weight'] ?? 40) }}" min="0" max="100" required
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 focus:border-indigo-500 text-sm font-bold text-violet-600">
                <span class="text-[11px] text-slate-500 block mt-1">Default standar rekrutmen: 40%</span>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-base font-bold text-slate-900 mb-1">Standar Default Pelaksanaan Ujian</h3>
            <p class="text-xs text-slate-500 mb-4">Nilai passing grade dan durasi acuan untuk paket tes baru</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Default Passing Grade (0-100)</label>
                    <input type="number" step="0.1" name="default_passing_grade" value="{{ old('default_passing_grade', $settings['default_passing_grade'] ?? 70) }}" min="0" max="100" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-bold text-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Default Durasi Psikotes (Menit)</label>
                    <input type="number" name="default_duration_minutes" value="{{ old('default_duration_minutes', $settings['default_duration_minutes'] ?? 50) }}" min="5" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-bold">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Perusahaan / Organisasi</label>
            <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? 'PT Rekrutmen Cipta Karir Indonesia') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-slate-100">
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition">
                Simpan Konfigurasi & Hitung Ulang Ranking
            </button>
        </div>
    </form>
</div>
@endsection
