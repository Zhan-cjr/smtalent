@extends('layouts.hrd', [
    'title' => 'Dashboard HRD & Rekrutmen',
    'headerTitle' => 'Dashboard Utama Rekrutmen',
    'headerSubtitle' => 'Ringkasan performa kandidat, statistik seleksi, dan status pipeline'
])

@section('content')
<div class="space-y-8">
    <!-- KPI Stat Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Kandidat -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pelamar</p>
                    <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalCandidates }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold">
                    👥
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                <span class="font-semibold text-indigo-600">{{ $countRegistered }}</span> Menunggu tes
            </div>
        </div>

        <!-- Selesai Psikotes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Lulus Psikotes</p>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $countPassedPsychotest }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold">
                    🎯
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                <span class="font-semibold text-emerald-600">Nilai &ge; Passing Grade</span>
            </div>
        </div>

        <!-- Tahap Interview -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tahap Interview</p>
                    <p class="text-3xl font-extrabold text-violet-600 mt-1">{{ $countInterview }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-2xl font-bold">
                    🎙️
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                <span class="font-semibold text-violet-600">8 Aspek Penilaian</span>
            </div>
        </div>

        <!-- Keputusan Lolos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kandidat Lolos (60:40)</p>
                    <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $countLolos }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl font-bold">
                    🏆
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                <span class="font-semibold text-amber-600">{{ $countCadangan }} Cadangan</span> | <span class="font-semibold text-rose-500">{{ $countTidakLolos }} Tidak Lolos</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart: Kandidat per Posisi -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Distribusi Pelamar per Posisi</h3>
                <span class="text-xs text-slate-400 font-medium">Berdasarkan Lowongan Terdaftar</span>
            </div>
            <div class="h-64 relative">
                <canvas id="positionChart"></canvas>
            </div>
        </div>

        <!-- Chart: Distribusi Nilai Psikotes -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Distribusi Skor Psikotes</h3>
                <span class="text-xs text-slate-400 font-medium">Skala 0 - 100</span>
            </div>
            <div class="h-64 relative">
                <canvas id="scoreDistChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Candidates & Activity Logs Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Candidates (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Kandidat Terbaru</h3>
                    <p class="text-xs text-slate-500">Data pelamar yang baru mendaftar atau menyelesaikan tes</p>
                </div>
                <a href="{{ route('hrd.candidates.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="py-3 px-5">Nama Kandidat</th>
                            <th class="py-3 px-4">Posisi</th>
                            <th class="py-3 px-4">Nilai Psikotes</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($recentCandidates as $cand)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-slate-900">{{ $cand->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $cand->email }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-600">
                                    {{ $cand->vacancy?->position?->name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($cand->final_psychotest_score !== null)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $cand->final_psychotest_score >= 70 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $cand->final_psychotest_score }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">Belum tes</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold 
                                        @if($cand->status === 'REGISTERED') bg-slate-100 text-slate-700
                                        @elseif($cand->status === 'PSIKOTES') bg-amber-100 text-amber-800
                                        @elseif($cand->status === 'LULUS_PSIKOTES') bg-blue-100 text-blue-800
                                        @elseif($cand->status === 'INTERVIEW' || $cand->status === 'LULUS_INTERVIEW') bg-violet-100 text-violet-800
                                        @elseif($cand->status === 'DITERIMA' || $cand->final_status === 'LOLOS') bg-emerald-100 text-emerald-800
                                        @else bg-rose-100 text-rose-800 @endif">
                                        {{ str_replace('_', ' ', $cand->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <a href="{{ route('hrd.candidates.show', $cand->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg text-indigo-600 hover:bg-indigo-50 border border-indigo-200 transition">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-xs text-slate-400 font-medium">Belum ada data kandidat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Audit Logs (1 Col) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Aktivitas Sistem</h3>
                    <p class="text-xs text-slate-500">Log audit riwayat keamanan & perubahan data</p>
                </div>
                <a href="{{ route('hrd.audit-logs.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="p-5 flex-1 overflow-y-auto divide-y divide-slate-100 space-y-3.5">
                @forelse($recentLogs as $log)
                    <div class="pt-3 first:pt-0 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                            {{ substr($log->action, 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-900 leading-tight truncate">{{ $log->action }}</p>
                            <p class="text-[11px] text-slate-500 leading-snug break-words mt-0.5">{{ $log->description }}</p>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-xs text-slate-400 py-6">Belum ada catatan aktivitas.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart 1: Kandidat per Posisi
        const ctxPos = document.getElementById('positionChart');
        if (ctxPos) {
            new Chart(ctxPos, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($positionLabels) !!},
                    datasets: [{
                        label: 'Jumlah Pelamar',
                        data: {!! json_encode($positionData) !!},
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        barThickness: 28,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Chart 2: Distribusi Nilai
        const ctxScore = document.getElementById('scoreDistChart');
        if (ctxScore) {
            new Chart(ctxScore, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_keys($scoreRanges)) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($scoreRanges)) !!},
                        backgroundColor: ['#f43f5e', '#fbbf24', '#3b82f6', '#10b981'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    },
                    cutout: '70%',
                }
            });
        }
    });
</script>
@endpush
@endsection
