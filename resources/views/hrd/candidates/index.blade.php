@extends('layouts.hrd', [
    'title' => 'Daftar Kandidat',
    'headerTitle' => 'Manajemen Kandidat Pelamar',
    'headerSubtitle' => 'Pantau status pengerjaan psikotes, jadwal interview, dan hasil seleksi akhir'
])

@section('content')
<div class="space-y-6">
    <!-- Filters & Search -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Semua Pelamar Terdaftar (Total: {{ $candidates->total() }})</h3>
                <p class="text-xs text-slate-500">Filter berdasarkan status seleksi, lowongan batch, atau pencarian nama</p>
            </div>
            <a href="{{ route('hrd.reports.export.excel') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                <span>📊</span> Export Rekap Excel
            </a>
        </div>

        <form action="{{ route('hrd.candidates.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 border-t border-slate-100">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status Seleksi</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="REGISTERED" {{ $status === 'REGISTERED' ? 'selected' : '' }}>REGISTERED (Menunggu Tes)</option>
                    <option value="PSIKOTES" {{ $status === 'PSIKOTES' ? 'selected' : '' }}>PSIKOTES (Sedang Tes)</option>
                    <option value="LULUS_PSIKOTES" {{ $status === 'LULUS_PSIKOTES' ? 'selected' : '' }}>LULUS PSIKOTES</option>
                    <option value="INTERVIEW" {{ $status === 'INTERVIEW' ? 'selected' : '' }}>INTERVIEW</option>
                    <option value="LULUS_INTERVIEW" {{ $status === 'LULUS_INTERVIEW' ? 'selected' : '' }}>LULUS INTERVIEW</option>
                    <option value="DITERIMA" {{ $status === 'DITERIMA' ? 'selected' : '' }}>DITERIMA / LOLOS</option>
                    <option value="CADANGAN" {{ $status === 'CADANGAN' ? 'selected' : '' }}>CADANGAN</option>
                    <option value="TIDAK_LULUS" {{ $status === 'TIDAK_LULUS' ? 'selected' : '' }}>TIDAK LULUS</option>
                </select>
            </div>

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
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Cari Nama / Email / HP</label>
                <div class="flex items-center gap-1.5">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500">
                    <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">Cari</button>
                    @if($status || $vacancyId || $positionId || $search)
                        <a href="{{ route('hrd.candidates.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold" title="Reset">✕</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Candidate Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- Desktop Table View -->
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3.5 px-6">Nama & Kontak</th>
                        <th class="py-3.5 px-6">Posisi Dilamar</th>
                        <th class="py-3.5 px-6 text-center">Nilai Psikotes</th>
                        <th class="py-3.5 px-6 text-center">Nilai Interview</th>
                        <th class="py-3.5 px-6 text-center">Nilai Akhir (60:40)</th>
                        <th class="py-3.5 px-6 text-center">Status Tahapan</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($candidates as $c)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $c->name }}</div>
                                <div class="text-xs text-slate-400">{{ $c->email }} | {{ $c->phone }}</div>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-800 text-xs">
                                <span class="bg-slate-100 px-2.5 py-1 rounded-lg">{{ $c->vacancy?->position?->name ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($c->final_psychotest_score !== null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $c->final_psychotest_score >= 70 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $c->final_psychotest_score }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($c->final_interview_score !== null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-violet-100 text-violet-800">
                                        {{ $c->final_interview_score }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($c->final_score !== null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800">
                                        {{ $c->final_score }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold 
                                    @if($c->status === 'REGISTERED') bg-slate-100 text-slate-700
                                    @elseif($c->status === 'PSIKOTES') bg-amber-100 text-amber-800
                                    @elseif($c->status === 'LULUS_PSIKOTES') bg-blue-100 text-blue-800
                                    @elseif($c->status === 'INTERVIEW' || $c->status === 'LULUS_INTERVIEW') bg-violet-100 text-violet-800
                                    @elseif($c->status === 'DITERIMA' || $c->final_status === 'LOLOS') bg-emerald-100 text-emerald-800
                                    @elseif($c->status === 'CADANGAN') bg-amber-100 text-amber-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ str_replace('_', ' ', $c->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                                @if($c->status === 'LULUS_PSIKOTES')
                                    <a href="{{ route('hrd.interviews.create', ['candidate_id' => $c->id]) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition">
                                        Undang Interview
                                    </a>
                                @endif
                                <a href="{{ route('hrd.candidates.show', $c->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-indigo-600 hover:bg-indigo-50 border border-indigo-200 transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-slate-400 font-medium">Belum ada data kandidat pelamar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($candidates as $c)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $c->name }}</h4>
                            <p class="text-xs text-slate-400">{{ $c->phone }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold 
                            @if($c->status === 'REGISTERED') bg-slate-100 text-slate-700
                            @elseif($c->status === 'PSIKOTES') bg-amber-100 text-amber-800
                            @elseif($c->status === 'LULUS_PSIKOTES') bg-blue-100 text-blue-800
                            @elseif($c->status === 'INTERVIEW' || $c->status === 'LULUS_INTERVIEW') bg-violet-100 text-violet-800
                            @elseif($c->status === 'DITERIMA' || $c->final_status === 'LOLOS') bg-emerald-100 text-emerald-800
                            @elseif($c->status === 'CADANGAN') bg-amber-100 text-amber-800
                            @else bg-rose-100 text-rose-800 @endif">
                            {{ str_replace('_', ' ', $c->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-center">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Psikotes</span>
                            <span class="text-xs font-black {{ ($c->final_psychotest_score ?? 0) >= 70 ? 'text-emerald-600' : 'text-slate-700' }}">
                                {{ $c->final_psychotest_score ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Interview</span>
                            <span class="text-xs font-black text-violet-600">
                                {{ $c->final_interview_score ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Akhir</span>
                            <span class="text-xs font-black text-indigo-600">
                                {{ $c->final_score ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <span class="text-slate-500 font-medium truncate max-w-[160px]">{{ $c->vacancy?->position?->name ?? '-' }}</span>
                        <div class="flex items-center gap-1.5">
                            @if($c->status === 'LULUS_PSIKOTES')
                                <a href="{{ route('hrd.interviews.create', ['candidate_id' => $c->id]) }}" class="px-2.5 py-1.5 text-xs font-bold rounded-lg bg-indigo-600 text-white">
                                    Interview
                                </a>
                            @endif
                            <a href="{{ route('hrd.candidates.show', $c->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-indigo-600 bg-indigo-50 border border-indigo-200">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">Belum ada data kandidat pelamar.</div>
            @endforelse
        </div>

        @if($candidates->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $candidates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
