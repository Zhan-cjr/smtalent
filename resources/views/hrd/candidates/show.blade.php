@extends('layouts.hrd', [
    'title' => 'Detail Kandidat - ' . $candidate->name,
    'headerTitle' => 'Profil & Lembar Evaluasi Kandidat',
    'headerSubtitle' => 'Rincian lengkap hasil psikotes, wawancara 8 aspek, dan kalkulasi nilai akhir'
])

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-2xl shadow-md shadow-indigo-600/20">
                {{ substr($candidate->name, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xl font-extrabold text-slate-900">{{ $candidate->name }}</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold 
                        @if($candidate->status === 'DITERIMA' || $candidate->final_status === 'LOLOS') bg-emerald-100 text-emerald-800
                        @elseif($candidate->status === 'CADANGAN') bg-amber-100 text-amber-800
                        @elseif($candidate->status === 'TIDAK_LULUS' || $candidate->final_status === 'TIDAK_LOLOS') bg-rose-100 text-rose-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ str_replace('_', ' ', $candidate->status) }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Melamar: <strong class="text-slate-800">{{ $candidate->vacancy?->position?->name }}</strong> ({{ $candidate->vacancy?->title }})</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('hrd.reports.candidate.pdf', $candidate->id) }}" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                <span>🖨️</span> Cetak Lembar PDF
            </a>
            <a href="{{ route('hrd.candidates.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- 3 Big Metric Cards: Psikotes, Interview, Final Score -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- 1. Skor Psikotes (60%) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs relative overflow-hidden">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">1. Nilai Psikotes (Bobot 60%)</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-slate-900">{{ $candidate->final_psychotest_score ?? '0.00' }}</span>
                <span class="text-xs font-semibold text-slate-400">/ 100</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Kontribusi Skor:</span>
                <span class="font-bold text-indigo-600">{{ round(floatval($candidate->final_psychotest_score ?? 0) * 0.60, 2) }} Poin</span>
            </div>
        </div>

        <!-- 2. Skor Interview (40%) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs relative overflow-hidden">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">2. Nilai Interview (Bobot 40%)</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-violet-600">{{ $candidate->final_interview_score ?? '0.00' }}</span>
                <span class="text-xs font-semibold text-slate-400">/ 100</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Kontribusi Skor:</span>
                <span class="font-bold text-violet-600">{{ round(floatval($candidate->final_interview_score ?? 0) * 0.40, 2) }} Poin</span>
            </div>
        </div>

        <!-- 3. Nilai Akhir & Ranking -->
        <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white p-6 rounded-3xl shadow-xl relative overflow-hidden">
            <span class="text-xs font-bold uppercase tracking-wider text-indigo-300">Nilai Akhir & Ranking</span>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-white">{{ $candidate->final_score ?? '0.00' }}</span>
                    <span class="text-xs font-semibold text-indigo-200">/ 100</span>
                </div>
                @if($candidate->final_rank)
                    <div class="px-3 py-1 rounded-xl bg-amber-400 text-slate-950 font-black text-sm">
                        RANK #{{ $candidate->final_rank }}
                    </div>
                @endif
            </div>
            <div class="mt-3 pt-3 border-t border-slate-700/80 flex items-center justify-between text-xs">
                <span class="text-slate-300">Keputusan Final:</span>
                <span class="font-bold text-amber-300">{{ $candidate->final_status ?: 'Dalam Proses' }}</span>
            </div>
        </div>
    </div>

    <!-- 2 Column Details: Profile Data & Status Changer -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Biodata Kandidat -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-900 pb-2 border-b border-slate-100">Biodata Pelamar</h4>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block font-semibold">Email:</span>
                    <span class="font-bold text-slate-800">{{ $candidate->email }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">No. WhatsApp / HP:</span>
                    <span class="font-bold text-slate-800">{{ $candidate->phone ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">NIK KTP:</span>
                    <span class="font-bold text-slate-800">{{ $candidate->nik ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Jenis Kelamin:</span>
                    <span class="font-bold text-slate-800">{{ $candidate->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Pendidikan:</span>
                    <span class="font-bold text-slate-800">{{ $candidate->education ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Alamat Domisili:</span>
                    <span class="font-medium text-slate-800">{{ $candidate->address ?: '-' }}</span>
                </div>
            </div>

            <!-- Form Ubah Status Manual -->
            <div class="pt-4 border-t border-slate-100">
                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">Ubah Status Seleksi</h5>
                <form action="{{ route('hrd.candidates.status', $candidate->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:border-indigo-500">
                            <option value="REGISTERED" {{ $candidate->status === 'REGISTERED' ? 'selected' : '' }}>REGISTERED</option>
                            <option value="PSIKOTES" {{ $candidate->status === 'PSIKOTES' ? 'selected' : '' }}>PSIKOTES</option>
                            <option value="LULUS_PSIKOTES" {{ $candidate->status === 'LULUS_PSIKOTES' ? 'selected' : '' }}>LULUS PSIKOTES</option>
                            <option value="INTERVIEW" {{ $candidate->status === 'INTERVIEW' ? 'selected' : '' }}>INTERVIEW</option>
                            <option value="LULUS_INTERVIEW" {{ $candidate->status === 'LULUS_INTERVIEW' ? 'selected' : '' }}>LULUS INTERVIEW</option>
                            <option value="DITERIMA" {{ $candidate->status === 'DITERIMA' ? 'selected' : '' }}>DITERIMA (LOLOS)</option>
                            <option value="CADANGAN" {{ $candidate->status === 'CADANGAN' ? 'selected' : '' }}>CADANGAN</option>
                            <option value="TIDAK_LULUS" {{ $candidate->status === 'TIDAK_LULUS' ? 'selected' : '' }}>TIDAK LULUS</option>
                        </select>
                    </div>
                    <div>
                        <select name="final_status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:border-indigo-500">
                            <option value="">-- Keputusan Akhir --</option>
                            <option value="LOLOS" {{ $candidate->final_status === 'LOLOS' ? 'selected' : '' }}>LOLOS SELEKSI</option>
                            <option value="CADANGAN" {{ $candidate->final_status === 'CADANGAN' ? 'selected' : '' }}>CADANGAN</option>
                            <option value="TIDAK_LOLOS" {{ $candidate->final_status === 'TIDAK_LOLOS' ? 'selected' : '' }}>TIDAK LOLOS</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Rincian Hasil Psikotes & Interview (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 1. Detail Hasil Psikotes -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-900">Rincian Hasil Psikotes</h4>
                        <p class="text-xs text-slate-500">Statistik jawaban lembar soal ujian online</p>
                    </div>
                    @if($latestAttempt)
                        <span class="text-xs font-semibold text-slate-500">
                            Durasi: {{ round($latestAttempt->duration_seconds_used / 60) }} Menit
                        </span>
                    @endif
                </div>

                @if($latestAttempt)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Soal</span>
                            <span class="text-lg font-black text-slate-900">{{ $latestAttempt->total_questions }}</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-100 text-center">
                            <span class="text-[10px] uppercase font-bold text-emerald-600 block">Benar</span>
                            <span class="text-lg font-black text-emerald-700">{{ $latestAttempt->total_correct }}</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-rose-50 border border-rose-100 text-center">
                            <span class="text-[10px] uppercase font-bold text-rose-600 block">Salah</span>
                            <span class="text-lg font-black text-rose-700">{{ $latestAttempt->total_wrong }}</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-amber-50 border border-amber-100 text-center">
                            <span class="text-[10px] uppercase font-bold text-amber-600 block">Tidak Dijawab</span>
                            <span class="text-lg font-black text-amber-700">{{ $latestAttempt->total_unanswered }}</span>
                        </div>
                    </div>

                    <!-- Aspek Sikap & Perilaku Kerja (Likert Profiling) -->
                    @if(!empty($latestAttempt->aspect_scores_json))
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <h5 class="text-xs font-bold uppercase tracking-wider text-slate-700">Indikator Sikap & Perilaku Kerja (Skala 1-5):</h5>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($latestAttempt->aspect_scores_json as $aspect => $data)
                                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                        <div class="flex items-center justify-between text-xs mb-1.5">
                                            <span class="font-bold text-slate-800">{{ $aspect }}</span>
                                            <span class="font-mono font-bold text-indigo-600">{{ $data['score_100'] ?? $data['average'] }} / 100</span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
                                            <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $data['score_100'] ?? ($data['average'] * 20) }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-xs text-slate-400 text-center py-6">Kandidat belum mengikuti sesi ujian psikotes.</p>
                @endif
            </div>

            <!-- 2. Detail Hasil Wawancara (8 Aspek) -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-900">Rincian Hasil Wawancara HRD</h4>
                        <p class="text-xs text-slate-500">Evaluasi 8 komponen kompetensi, kelebihan & rekomendasi</p>
                    </div>
                    @if($candidate->status === 'LULUS_PSIKOTES')
                        <a href="{{ route('hrd.interviews.create', ['candidate_id' => $candidate->id]) }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs">
                            Jadwalkan Interview
                        </a>
                    @endif
                </div>

                @if($latestInterview && $latestInterview->scores->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                        @foreach($latestInterview->scores as $sc)
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block truncate">{{ $sc->aspect_name }}</span>
                                <span class="text-base font-extrabold text-violet-700">{{ $sc->score }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 text-xs pt-3 border-t border-slate-100">
                        @if($latestInterview->recommendation)
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 font-semibold">Rekomendasi Interviewer:</span>
                                <span class="font-extrabold px-2 py-0.5 rounded-md {{ $latestInterview->recommendation === 'DISARANKAN' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $latestInterview->recommendation }}
                                </span>
                            </div>
                        @endif

                        @if($latestInterview->strengths)
                            <div>
                                <span class="text-slate-400 block font-semibold">Kelebihan Kandidat:</span>
                                <p class="text-slate-700 mt-0.5">{{ $latestInterview->strengths }}</p>
                            </div>
                        @endif

                        @if($latestInterview->weaknesses)
                            <div>
                                <span class="text-slate-400 block font-semibold">Catatan / Hal yang Perlu Dikembangkan:</span>
                                <p class="text-slate-700 mt-0.5">{{ $latestInterview->weaknesses }}</p>
                            </div>
                        @endif
                    </div>
                @elseif($latestInterview)
                    <div class="p-4 rounded-2xl bg-violet-50 border border-violet-100 text-xs text-violet-800 flex items-center justify-between">
                        <div>
                            <span class="font-bold">Interview Terjadwal:</span> {{ $latestInterview->scheduled_at->format('d M Y - H:i') }} di {{ $latestInterview->location }}
                        </div>
                        <a href="{{ route('hrd.interviews.score', $latestInterview->id) }}" class="px-3 py-1.5 rounded-lg bg-violet-600 text-white font-bold hover:bg-violet-700">
                            Input Nilai Sekarang
                        </a>
                    </div>
                @else
                    <p class="text-xs text-slate-400 text-center py-6">Kandidat belum dijadwalkan wawancara.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
