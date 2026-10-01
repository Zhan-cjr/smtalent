@extends('layouts.hrd', [
    'title' => 'Interview & Penilaian',
    'headerTitle' => 'Antrean & Penilaian Wawancara (Interview)',
    'headerSubtitle' => 'Pilih kandidat langsung berdasarkan urutan selesai tes psikotes, kuncian multi-HRD, dan input penilaian 8 aspek.'
])

@section('content')
<div class="space-y-5 sm:space-y-6">
    <!-- Header Summary & Navigation Tabs -->
    <div class="bg-white p-4 sm:p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-black text-slate-900">Workspace Wawancara Kandidat</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    HRD dapat langsung memilih kandidat untuk diwawancarai. Kandidat yang dipilih akan otomatis terkunci untuk HRD bersangkutan sehingga HRD lain tidak dapat memilihnya secara bersamaan.
                </p>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 self-start md:self-center shrink-0">
                <a href="{{ route('hrd.interviews.create') }}" class="px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5">
                    <span>📅</span> <span>Jadwalkan Manual</span>
                </a>
            </div>
        </div>

        <!-- Navigation Tabs (Mobile-scrollable) -->
        <div class="flex border-b border-slate-100 text-xs sm:text-sm font-semibold gap-4 sm:gap-6 overflow-x-auto whitespace-nowrap custom-scrollbar pb-0.5">
            <a href="{{ route('hrd.interviews.index', ['tab' => 'candidates', 'search' => $search, 'position_id' => $positionId]) }}"
               class="pb-2.5 sm:pb-3 border-b-2 flex items-center gap-1.5 sm:gap-2 transition {{ $tab === 'candidates' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                <span>👥</span>
                <span>Antrean Siap Interview</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] sm:text-xs {{ $tab === 'candidates' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $candidates->total() }}
                </span>
            </a>
            <a href="{{ route('hrd.interviews.index', ['tab' => 'sessions', 'search' => $search, 'status' => $interviewStatus]) }}"
               class="pb-2.5 sm:pb-3 border-b-2 flex items-center gap-1.5 sm:gap-2 transition {{ $tab === 'sessions' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                <span>🎙️</span>
                <span>Sesi & Riwayat Interview</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] sm:text-xs {{ $tab === 'sessions' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $interviews->total() }}
                </span>
            </a>
        </div>
    </div>

    <!-- TAB 1: ANTREAN KANDIDAT SIAP INTERVIEW -->
    @if($tab === 'candidates')
        <!-- Search & Filter Bar -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 sm:gap-4">
            <form action="{{ route('hrd.interviews.index') }}" method="GET" class="w-full md:w-auto flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
                <input type="hidden" name="tab" value="candidates">
                
                <div class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">🔍</span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, no HP, NIK..."
                           class="w-full pl-10 pr-4 py-2 sm:py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-xs font-medium">
                </div>

                <select name="position_id" onchange="this.form.submit()" class="w-full sm:w-56 px-3 py-2 sm:py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-xs font-medium">
                    <option value="">-- Semua Posisi Jabatan --</option>
                    @foreach($positions as $p)
                        <option value="{{ $p->id }}" {{ $positionId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 sm:flex-none px-4 py-2 sm:py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                        Cari
                    </button>

                    @if($search || $positionId)
                        <a href="{{ route('hrd.interviews.index', ['tab' => 'candidates']) }}" class="px-3 py-2 text-xs text-rose-500 hover:underline font-semibold whitespace-nowrap">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <div class="text-[11px] text-slate-400 font-semibold self-start md:self-center">
                Urutan: <strong class="text-indigo-600">Selesai Psikotes Terawal &uarr;</strong>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="py-3.5 px-6">Urutan Selesai</th>
                            <th class="py-3.5 px-6">Kandidat</th>
                            <th class="py-3.5 px-6">Posisi Dilamar</th>
                            <th class="py-3.5 px-6 text-center">Waktu Selesai Psikotes</th>
                            <th class="py-3.5 px-6 text-center">Skor Psikotes</th>
                            <th class="py-3.5 px-6 text-center">Status Wawancara</th>
                            <th class="py-3.5 px-6 text-right">Aksi HRD</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($candidates as $index => $cand)
                            @php
                                $activeItw = $cand->activeInterview;
                                $latestItw = $cand->latestInterview;
                                $isClaimedByOther = $activeItw && $activeItw->interviewer_id && $activeItw->interviewer_id !== auth()->id();
                                $isClaimedByMe = $activeItw && $activeItw->interviewer_id === auth()->id();
                                $isCompleted = $latestItw && $latestItw->status === 'completed';
                                
                                $rankNumber = ($candidates->currentPage() - 1) * $candidates->perPage() + $index + 1;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition {{ $isClaimedByMe ? 'bg-indigo-50/30' : ($isClaimedByOther ? 'bg-slate-50/80 opacity-85' : '') }}">
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl {{ $rankNumber <= 3 ? 'bg-amber-100 text-amber-800 font-extrabold' : 'bg-slate-100 text-slate-700' }} text-xs">
                                        #{{ $rankNumber }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-extrabold text-slate-900">{{ $cand->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $cand->email }} &bull; {{ $cand->phone }}</div>
                                    @if($cand->nik)
                                        <div class="text-[10px] text-slate-400 font-mono">NIK: {{ $cand->nik }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-800">
                                        {{ $cand->vacancy?->position?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center text-xs">
                                    @if($cand->first_submitted_at)
                                        <div class="font-bold text-slate-900">
                                            {{ \Carbon\Carbon::parse($cand->first_submitted_at)->format('d M Y') }}
                                        </div>
                                        <div class="text-indigo-600 font-mono font-semibold">
                                            {{ \Carbon\Carbon::parse($cand->first_submitted_at)->format('H:i:s') }} WIB
                                        </div>
                                    @elseif($cand->latestAttempt?->submitted_at)
                                        <div class="font-bold text-slate-900">
                                            {{ $cand->latestAttempt->submitted_at->format('d M Y') }}
                                        </div>
                                        <div class="text-indigo-600 font-mono font-semibold">
                                            {{ $cand->latestAttempt->submitted_at->format('H:i:s') }} WIB
                                        </div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($cand->final_psychotest_score !== null)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black {{ $cand->final_psychotest_score >= 70 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $cand->final_psychotest_score }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">Belum ada</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($isClaimedByMe)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-800 border border-indigo-200 animate-pulse">
                                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                            Sedang Anda Interview
                                        </span>
                                    @elseif($isClaimedByOther)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                            🔒 Di-interview {{ $activeItw->interviewer?->name ?? 'HRD Lain' }}
                                        </span>
                                    @elseif($isCompleted)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            ✅ Selesai (Skor: {{ $latestItw->average_score }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                            ✨ Siap Diwawancarai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap space-x-1.5">
                                    @if($isClaimedByMe)
                                        <a href="{{ route('hrd.interviews.score', $activeItw->id) }}"
                                           class="px-3.5 py-2 text-xs font-extrabold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition inline-flex items-center gap-1">
                                            <span>📝</span> Lanjutkan Penilaian
                                        </a>
                                        <form action="{{ route('hrd.interviews.release', $activeItw->id) }}" method="POST" class="inline" onsubmit="return confirm('Lepas kandidat ini agar HRD lain dapat memilihnya?');">
                                            @csrf
                                            <button type="submit" title="Lepas kandidat ini"
                                                    class="px-2.5 py-2 text-xs font-bold rounded-xl text-rose-600 hover:bg-rose-50 border border-rose-200 transition">
                                                Lepas
                                            </button>
                                        </form>
                                    @elseif($isClaimedByOther)
                                        <button type="button" disabled title="Kandidat sedang dipilih/di-interview oleh {{ $activeItw->interviewer?->name }}"
                                                class="px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed inline-flex items-center gap-1">
                                            <span>🔒</span> Terkunci ({{ Str::limit($activeItw->interviewer?->name, 12) }})
                                        </button>
                                    @elseif($isCompleted)
                                        <a href="{{ route('hrd.interviews.score', $latestItw->id) }}"
                                           class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                            Lihat / Edit Nilai
                                        </a>
                                    @else
                                        <form action="{{ route('hrd.interviews.claim') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="candidate_id" value="{{ $cand->id }}">
                                            <button type="submit"
                                                    class="px-4 py-2 text-xs font-extrabold rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white shadow-md shadow-indigo-600/20 transition transform hover:-translate-y-0.5 inline-flex items-center gap-1.5">
                                                <span>🎯</span> Pilih & Mulai Interview
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-xs text-slate-400 font-medium">
                                    <div class="text-3xl mb-2">👥</div>
                                    Tidak ada kandidat dalam antrean psikotes yang cocok dengan pencarian Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($candidates->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $candidates->links() }}
                </div>
            @endif
        </div>

        <!-- Mobile Card View (Optimized for Smartphone HP) -->
        <div class="md:hidden space-y-3">
            @forelse($candidates as $index => $cand)
                @php
                    $activeItw = $cand->activeInterview;
                    $latestItw = $cand->latestInterview;
                    $isClaimedByOther = $activeItw && $activeItw->interviewer_id && $activeItw->interviewer_id !== auth()->id();
                    $isClaimedByMe = $activeItw && $activeItw->interviewer_id === auth()->id();
                    $isCompleted = $latestItw && $latestItw->status === 'completed';
                    
                    $rankNumber = ($candidates->currentPage() - 1) * $candidates->perPage() + $index + 1;
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3 {{ $isClaimedByMe ? 'ring-2 ring-indigo-500 bg-indigo-50/20' : ($isClaimedByOther ? 'bg-slate-50/70' : '') }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl {{ $rankNumber <= 3 ? 'bg-amber-100 text-amber-800 font-black' : 'bg-slate-100 text-slate-700' }} text-xs shrink-0">
                                #{{ $rankNumber }}
                            </span>
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-slate-900 text-sm leading-tight truncate">{{ $cand->name }}</h4>
                                <p class="text-[11px] text-slate-400 truncate">{{ $cand->phone }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-800 shrink-0">
                            {{ $cand->vacancy?->position?->name ?? '-' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Selesai Psikotes</span>
                            <strong class="text-slate-800 font-mono text-[11px]">
                                @if($cand->first_submitted_at)
                                    {{ \Carbon\Carbon::parse($cand->first_submitted_at)->format('d M H:i') }} WIB
                                @elseif($cand->latestAttempt?->submitted_at)
                                    {{ $cand->latestAttempt->submitted_at->format('d M H:i') }} WIB
                                @else
                                    -
                                @endif
                            </strong>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Skor Psikotes</span>
                            <strong class="text-xs {{ ($cand->final_psychotest_score ?? 0) >= 70 ? 'text-emerald-700' : 'text-slate-700' }}">
                                {{ $cand->final_psychotest_score !== null ? $cand->final_psychotest_score : 'Belum ada' }}
                            </strong>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100 text-xs">
                        <div class="min-w-0">
                            @if($isClaimedByMe)
                                <span class="text-indigo-600 font-extrabold text-[11px] flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span> Sedang Anda Interview
                                </span>
                            @elseif($isClaimedByOther)
                                <span class="text-amber-800 font-bold text-[11px]">
                                    🔒 {{ Str::limit($activeItw->interviewer?->name, 12) }}
                                </span>
                            @elseif($isCompleted)
                                <span class="text-emerald-700 font-bold text-[11px]">
                                    ✅ Selesai ({{ $latestItw->average_score }})
                                </span>
                            @else
                                <span class="text-slate-400 text-[11px]">Siap Interview</span>
                            @endif
                        </div>

                        <div class="shrink-0">
                            @if($isClaimedByMe)
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('hrd.interviews.score', $activeItw->id) }}"
                                       class="px-3 py-1.5 text-xs font-extrabold rounded-xl bg-indigo-600 text-white shadow-xs">
                                        Nilai
                                    </a>
                                    <form action="{{ route('hrd.interviews.release', $activeItw->id) }}" method="POST" class="inline" onsubmit="return confirm('Lepas kandidat ini?');">
                                        @csrf
                                        <button type="submit" class="px-2 py-1.5 text-xs font-bold rounded-xl text-rose-600 border border-rose-200">
                                            Lepas
                                        </button>
                                    </form>
                                </div>
                            @elseif($isClaimedByOther)
                                <button type="button" disabled class="px-3 py-1.5 text-xs font-bold rounded-xl bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                                    🔒 Terkunci
                                </button>
                            @elseif($isCompleted)
                                <a href="{{ route('hrd.interviews.score', $latestItw->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 text-slate-700">
                                    Edit Nilai
                                </a>
                            @else
                                <form action="{{ route('hrd.interviews.claim') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="candidate_id" value="{{ $cand->id }}">
                                    <button type="submit" class="px-3.5 py-1.5 text-xs font-extrabold rounded-xl bg-indigo-600 text-white shadow-sm flex items-center gap-1">
                                        <span>🎯</span> Pilih
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white rounded-2xl border border-slate-200 text-xs text-slate-400">
                    Tidak ada kandidat dalam antrean psikotes.
                </div>
            @endforelse

            @if($candidates->hasPages())
                <div class="p-3 bg-white rounded-2xl border border-slate-200">
                    {{ $candidates->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: DAFTAR SESI & RIWAYAT INTERVIEW -->
    @if($tab === 'sessions')
        <!-- Filter Session Bar -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
            <form action="{{ route('hrd.interviews.index') }}" method="GET" class="w-full flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
                <input type="hidden" name="tab" value="sessions">

                <div class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">🔍</span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama kandidat / kontak..."
                           class="w-full pl-10 pr-4 py-2 sm:py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-xs font-medium">
                </div>

                <select name="status" onchange="this.form.submit()" class="w-full sm:w-48 px-3 py-2 sm:py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-xs font-medium">
                    <option value="">-- Semua Status Sesi --</option>
                    <option value="in_progress" {{ $interviewStatus === 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="scheduled" {{ $interviewStatus === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="completed" {{ $interviewStatus === 'completed' ? 'selected' : '' }}>Selesai Dinilai</option>
                    <option value="cancelled" {{ $interviewStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                <button type="submit" class="w-full sm:w-auto px-4 py-2 sm:py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Filter
                </button>
            </form>
        </div>

        <!-- Table of Interviews (Desktop) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="py-3.5 px-6">Kandidat</th>
                            <th class="py-3.5 px-6">Posisi Dilamar</th>
                            <th class="py-3.5 px-6">Waktu & Lokasi</th>
                            <th class="py-3.5 px-6">Interviewer HRD</th>
                            <th class="py-3.5 px-6 text-center">Rata-rata Skor</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($interviews as $itw)
                            @php
                                $isMine = $itw->interviewer_id === auth()->id();
                                $isLockedByOther = $itw->isLockedByOther();
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition {{ $isMine ? 'bg-indigo-50/20' : '' }}">
                                <td class="py-4 px-6">
                                    <div class="font-extrabold text-slate-900">{{ $itw->candidate->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $itw->candidate->phone }}</div>
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-800 text-xs">
                                    <span class="bg-slate-100 px-2.5 py-1 rounded-lg">{{ $itw->candidate->vacancy?->position?->name ?? '-' }}</span>
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-600">
                                    <div class="font-bold text-slate-900">{{ $itw->scheduled_at->format('d M Y - H:i') }} WIB</div>
                                    <div class="text-slate-400">{{ $itw->location }}</div>
                                </td>
                                <td class="py-4 px-6 text-xs font-semibold text-slate-700">
                                    <div class="flex items-center gap-1.5">
                                        <span>{{ $itw->interviewer?->name ?? 'HRD Team' }}</span>
                                        @if($isMine)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700">Anda</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($itw->average_score !== null)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-violet-100 text-violet-800">
                                            {{ $itw->average_score }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">Belum dinilai</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold 
                                        @if($itw->status === 'completed') bg-emerald-100 text-emerald-800
                                        @elseif($itw->status === 'in_progress') bg-indigo-100 text-indigo-800
                                        @elseif($itw->status === 'scheduled') bg-amber-100 text-amber-800
                                        @else bg-slate-100 text-slate-600 @endif">
                                        @if($itw->status === 'completed') Selesai Dinilai
                                        @elseif($itw->status === 'in_progress') Sedang Berjalan
                                        @elseif($itw->status === 'scheduled') Terjadwal
                                        @else Dibatalkan @endif
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap space-x-1.5">
                                    @if($isLockedByOther)
                                        <button type="button" disabled class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                                            🔒 Milik {{ Str::limit($itw->interviewer?->name, 10) }}
                                        </button>
                                    @else
                                        <a href="{{ route('hrd.interviews.score', $itw->id) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg {{ $itw->status === 'completed' ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs' }} transition">
                                            {{ $itw->status === 'completed' ? 'Edit Nilai' : 'Input Nilai' }}
                                        </a>
                                        @if($itw->status !== 'completed' && $isMine)
                                            <form action="{{ route('hrd.interviews.release', $itw->id) }}" method="POST" class="inline" onsubmit="return confirm('Lepas sesi interview ini?');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 text-xs font-bold rounded-lg text-rose-600 hover:bg-rose-50 border border-rose-200">
                                                    Lepas
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-xs text-slate-400 font-medium">Belum ada sesi wawancara yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($interviews->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $interviews->links() }}
                </div>
            @endif
        </div>

        <!-- Mobile Sessions View -->
        <div class="md:hidden space-y-3">
            @forelse($interviews as $itw)
                @php
                    $isMine = $itw->interviewer_id === auth()->id();
                    $isLockedByOther = $itw->isLockedByOther();
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs space-y-3 {{ $isMine ? 'ring-2 ring-indigo-500 bg-indigo-50/20' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-extrabold text-slate-900 text-sm leading-tight">{{ $itw->candidate->name }}</h4>
                            <p class="text-[11px] text-slate-400">{{ $itw->candidate->phone }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-800">
                            {{ $itw->candidate->vacancy?->position?->name ?? '-' }}
                        </span>
                    </div>

                    <div class="text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-[11px]">Waktu:</span>
                            <span class="font-bold text-slate-800">{{ $itw->scheduled_at->format('d M Y - H:i') }} WIB</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-[11px]">Interviewer:</span>
                            <span class="font-semibold text-slate-800">
                                {{ $itw->interviewer?->name ?? 'HRD Team' }} {{ $isMine ? '(Anda)' : '' }}
                            </span>
                        </div>
                        @if($itw->average_score !== null)
                            <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                                <span class="text-slate-400 text-[11px]">Rata-rata Skor:</span>
                                <span class="font-black text-violet-700">{{ $itw->average_score }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                            @if($itw->status === 'completed') bg-emerald-100 text-emerald-800
                            @elseif($itw->status === 'in_progress') bg-indigo-100 text-indigo-800
                            @elseif($itw->status === 'scheduled') bg-amber-100 text-amber-800
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ $itw->status === 'completed' ? 'Selesai Dinilai' : ($itw->status === 'in_progress' ? 'Sedang Berjalan' : 'Terjadwal') }}
                        </span>

                        <div>
                            @if($isLockedByOther)
                                <span class="text-[11px] text-slate-400 font-bold">🔒 Milik {{ Str::limit($itw->interviewer?->name, 10) }}</span>
                            @else
                                <a href="{{ route('hrd.interviews.score', $itw->id) }}"
                                   class="px-3 py-1.5 text-xs font-bold rounded-xl {{ $itw->status === 'completed' ? 'bg-slate-100 text-slate-700' : 'bg-indigo-600 text-white shadow-xs' }}">
                                    {{ $itw->status === 'completed' ? 'Edit Nilai' : 'Input Nilai' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white rounded-2xl border border-slate-200 text-xs text-slate-400">
                    Belum ada sesi wawancara yang terdaftar.
                </div>
            @endforelse

            @if($interviews->hasPages())
                <div class="p-3 bg-white rounded-2xl border border-slate-200">
                    {{ $interviews->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
