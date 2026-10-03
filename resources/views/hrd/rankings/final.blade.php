@extends('layouts.hrd', [
    'title' => 'Ranking Akhir Seleksi',
    'headerTitle' => 'Leaderboard & Ranking Akhir Seleksi',
    'headerSubtitle' => 'Kombinasi Nilai Psikotes (' . $psychotestWeight . '%) + Nilai Interview (' . $interviewWeight . '%)'
])

@section('content')
<div class="space-y-6">
    <!-- Top Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Hasil Akhir Keputusan Rekrutmen ({{ $rankings->count() }} Kandidat)</h3>
                <p class="text-xs text-slate-500">Ranking diurutkan dari Nilai Akhir tertinggi. Jika nilai sama, nilai psikotes dan kecepatan waktu menjadi tie-breaker.</p>
            </div>
            <a href="{{ route('hrd.reports.export.excel', request()->query()) }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                <span>📄</span> Export Laporan Excel
            </a>
        </div>

        <form action="{{ route('hrd.rankings.final') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-100">
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
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status Kelulusan Final</label>
                <select name="final_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Keputusan</option>
                    <option value="LOLOS" {{ $finalStatus === 'LOLOS' ? 'selected' : '' }}>LOLOS / Diterima</option>
                    <option value="CADANGAN" {{ $finalStatus === 'CADANGAN' ? 'selected' : '' }}>CADANGAN</option>
                    <option value="TIDAK_LOLOS" {{ $finalStatus === 'TIDAK_LOLOS' ? 'selected' : '' }}>TIDAK LOLOS</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Final Ranking Leaderboard -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3.5 px-6 text-center w-16">Rank</th>
                        <th class="py-3.5 px-6">Nama Kandidat</th>
                        <th class="py-3.5 px-6">Posisi Jabatan</th>
                        <th class="py-3.5 px-6 text-center">Psikotes ({{ $psychotestWeight }}%)</th>
                        <th class="py-3.5 px-6 text-center">Interview ({{ $interviewWeight }}%)</th>
                        <th class="py-3.5 px-6 text-center">Nilai Akhir</th>
                        <th class="py-3.5 px-6 text-center">Keputusan Seleksi</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($rankings as $index => $cand)
                        @php
                            $rank = $index + 1;
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
                            <td class="py-4 px-6 text-center font-semibold text-slate-700">
                                {{ $cand->final_psychotest_score ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-center font-semibold text-violet-700">
                                {{ $cand->final_interview_score ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="text-base font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-xl border border-indigo-100">
                                    {{ $cand->final_score ?? '-' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <form action="{{ route('hrd.rankings.decision', $cand->id) }}" method="POST" class="inline-flex items-center gap-1.5">
                                    @csrf
                                    <select name="final_status" onchange="this.form.submit()" class="px-2.5 py-1 rounded-xl text-xs font-bold border transition cursor-pointer
                                        @if($cand->final_status === 'LOLOS' || $cand->status === 'DITERIMA') bg-emerald-100 text-emerald-800 border-emerald-300
                                        @elseif($cand->final_status === 'CADANGAN' || $cand->status === 'CADANGAN') bg-amber-100 text-amber-800 border-amber-300
                                        @elseif($cand->final_status === 'TIDAK_LOLOS' || $cand->status === 'TIDAK_LULUS') bg-rose-100 text-rose-800 border-rose-300
                                        @else bg-slate-100 text-slate-700 border-slate-300 @endif">
                                        <option value="LOLOS" {{ ($cand->final_status === 'LOLOS' || $cand->status === 'DITERIMA') ? 'selected' : '' }}>🟢 LOLOS</option>
                                        <option value="CADANGAN" {{ ($cand->final_status === 'CADANGAN' || $cand->status === 'CADANGAN') ? 'selected' : '' }}>🟡 CADANGAN</option>
                                        <option value="TIDAK_LOLOS" {{ ($cand->final_status === 'TIDAK_LOLOS' || $cand->status === 'TIDAK_LULUS') ? 'selected' : '' }}>🔴 TIDAK LOLOS</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('hrd.candidates.show', $cand->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-indigo-600 hover:bg-indigo-50 border border-indigo-200 transition">
                                    Rincian
                                </a>
                                <a href="{{ route('hrd.reports.candidate.pdf', $cand->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-700 hover:bg-slate-100 border border-slate-200 transition" title="Unduh PDF">
                                    PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-xs text-slate-400 font-medium">Belum ada data kalkulasi ranking akhir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
