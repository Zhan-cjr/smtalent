@extends('layouts.hrd', [
    'title' => 'Ranking Hasil Psikotes',
    'headerTitle' => 'Ranking & Hasil Tes Psikotes',
    'headerSubtitle' => 'Peringkat otomatis berdasarkan nilai tertinggi psikotes (Passing Grade: ' . $passingGrade . ')'
])

@section('content')
<div class="space-y-6">
    <!-- Filter Topbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Leaderboard Psikotes ({{ $rankings->count() }} Kandidat)</h3>
                <p class="text-xs text-slate-500">Kandidat dengan nilai &ge; {{ $passingGrade }} berhak diundang ke tahapan wawancara</p>
            </div>
            <a href="{{ route('hrd.reports.export.excel') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                <span>📊</span> Export Rekap
            </a>
        </div>

        <form action="{{ route('hrd.rankings.psychotest') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-100">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Lowongan Batch</label>
                <select name="vacancy_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Lowongan</option>
                    @foreach($vacancies as $vac)
                        <option value="{{ $vac->id }}" {{ $vacancyId == $vac->id ? 'selected' : '' }}>{{ $vac->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Posisi Jabatan</label>
                <select name="position_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Posisi</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ $positionId == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status Kelulusan</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="LULUS_PSIKOTES" {{ $status === 'LULUS_PSIKOTES' ? 'selected' : '' }}>Lulus Psikotes</option>
                    <option value="TIDAK_LULUS" {{ $status === 'TIDAK_LULUS' ? 'selected' : '' }}>Tidak Lulus</option>
                    <option value="INTERVIEW" {{ $status === 'INTERVIEW' ? 'selected' : '' }}>Sudah Dijadwalkan Interview</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3.5 px-6 text-center w-16">Rank</th>
                        <th class="py-3.5 px-6">Nama Kandidat</th>
                        <th class="py-3.5 px-6">Posisi Jabatan</th>
                        <th class="py-3.5 px-6 text-center">Durasi Tes</th>
                        <th class="py-3.5 px-6 text-center">Skor Psikotes</th>
                        <th class="py-3.5 px-6 text-center">Hasil Passing Grade</th>
                        <th class="py-3.5 px-6 text-right">Aksi HRD</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($rankings as $index => $cand)
                        @php
                            $rank = $index + 1;
                            $isPassed = $cand->final_psychotest_score >= $passingGrade;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition {{ $rank <= 3 ? 'bg-indigo-50/20' : '' }}">
                            <td class="py-4 px-6 text-center">
                                @if($rank === 1)
                                    <span class="inline-flex w-8 h-8 rounded-xl bg-amber-400 text-amber-950 font-black items-center justify-center text-sm shadow-xs">🥇</span>
                                @elseif($rank === 2)
                                    <span class="inline-flex w-8 h-8 rounded-xl bg-slate-300 text-slate-900 font-black items-center justify-center text-sm shadow-xs">🥈</span>
                                @elseif($rank === 3)
                                    <span class="inline-flex w-8 h-8 rounded-xl bg-amber-700 text-white font-black items-center justify-center text-sm shadow-xs">🥉</span>
                                @else
                                    <span class="font-bold text-slate-400 text-xs">#{{ $rank }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $cand->name }}</div>
                                <div class="text-xs text-slate-400">{{ $cand->email }} | {{ $cand->phone }}</div>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-800 text-xs">
                                <span class="bg-slate-100 px-2.5 py-1 rounded-lg">{{ $cand->vacancy?->position?->name ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6 text-center text-xs text-slate-500 font-medium">
                                {{ $cand->latestAttempt ? round($cand->latestAttempt->duration_seconds_used / 60) . ' mnt' : '-' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="text-base font-black {{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $cand->final_psychotest_score }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($isPassed)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <span>✓</span> LULUS (PG: {{ $passingGrade }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        <span>✕</span> TIDAK LULUS
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap space-x-2">
                                @if($isPassed && in_array($cand->status, ['LULUS_PSIKOTES', 'REGISTERED']))
                                    <a href="{{ route('hrd.interviews.create', ['candidate_id' => $cand->id]) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition">
                                        Undang Interview
                                    </a>
                                @endif
                                <a href="{{ route('hrd.candidates.show', $cand->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-600 hover:bg-slate-100 border border-slate-200">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-slate-400 font-medium">Belum ada hasil psikotes yang terekam.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
