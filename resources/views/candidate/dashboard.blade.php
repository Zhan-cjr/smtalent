@extends('layouts.candidate', [
    'title' => 'Dashboard Seleksi Kandidat'
])

@section('content')
<div class="space-y-8">
    <!-- Welcome Header Banner -->
    <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-2 max-w-xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/30 text-indigo-200 border border-indigo-400/20">
                <span>⚡</span> Portal Rekrutmen Resmi
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ $candidate->name }}!</h2>
            <p class="text-xs sm:text-sm text-indigo-200">
                Anda terdaftar pada posisi <strong class="text-white">{{ $candidate->vacancy?->position?->name }}</strong> ({{ $candidate->vacancy?->title }}).
            </p>
        </div>

        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 text-center min-w-[180px]">
            <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-200 block">Status Lamaran Saat Ini</span>
            <span class="text-lg font-black text-amber-300 block mt-1">
                {{ str_replace('_', ' ', $candidate->status) }}
            </span>
        </div>
    </div>

    <!-- Selection Pipeline Steps (Linimasa Seleksi) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <h3 class="text-base font-bold text-slate-900 mb-6">Tahapan Proses Seleksi</h3>

        @php
            $steps = [
                ['key' => 'REGISTERED', 'label' => '1. Registrasi & Berkas', 'desc' => 'Pendaftaran akun & data diri berhasil'],
                ['key' => 'PSIKOTES', 'label' => '2. Ujian Psikotes Online', 'desc' => 'Pengerjaan 40 soal dalam 50 menit'],
                ['key' => 'INTERVIEW', 'label' => '3. Wawancara HRD', 'desc' => 'Evaluasi 8 kompetensi kerja'],
                ['key' => 'FINAL', 'label' => '4. Pengumuman Akhir', 'desc' => 'Keputusan penerimaan karyawan'],
            ];

            // Helper to get step status
            $currentStatus = $candidate->status;
            function getStepState($stepIndex, $currentStatus, $candidate) {
                if ($stepIndex === 0) return 'completed';
                if ($stepIndex === 1) {
                    if (in_array($currentStatus, ['LULUS_PSIKOTES', 'INTERVIEW', 'LULUS_INTERVIEW', 'DITERIMA', 'CADANGAN'])) return 'completed';
                    if ($currentStatus === 'PSIKOTES') return 'active';
                    if ($currentStatus === 'TIDAK_LULUS' && $candidate->final_psychotest_score !== null) return 'failed';
                    return 'pending';
                }
                if ($stepIndex === 2) {
                    if (in_array($currentStatus, ['LULUS_INTERVIEW', 'DITERIMA', 'CADANGAN'])) return 'completed';
                    if ($currentStatus === 'INTERVIEW') return 'active';
                    return 'pending';
                }
                if ($stepIndex === 3) {
                    if ($candidate->final_status === 'LOLOS' || $currentStatus === 'DITERIMA') return 'completed';
                    if ($candidate->final_status === 'CADANGAN' || $currentStatus === 'CADANGAN') return 'warning';
                    if ($candidate->final_status === 'TIDAK_LOLOS' || $currentStatus === 'TIDAK_LULUS') return 'failed';
                    return 'pending';
                }
                return 'pending';
            }
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($steps as $idx => $st)
                @php
                    $state = getStepState($idx, $currentStatus, $candidate);
                @endphp
                <div class="p-4 rounded-2xl border transition relative 
                    @if($state === 'completed') bg-emerald-50/50 border-emerald-200 text-emerald-900
                    @elseif($state === 'active') bg-indigo-50 border-indigo-300 text-indigo-950 shadow-sm
                    @elseif($state === 'failed') bg-rose-50 border-rose-200 text-rose-900
                    @elseif($state === 'warning') bg-amber-50 border-amber-200 text-amber-900
                    @else bg-slate-50 border-slate-200 text-slate-500 @endif">
                    
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold">{{ $st['label'] }}</span>
                        @if($state === 'completed')
                            <span class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">✓</span>
                        @elseif($state === 'active')
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-ping"></span>
                        @elseif($state === 'failed')
                            <span class="w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs font-bold">✕</span>
                        @endif
                    </div>
                    <p class="text-[11px] leading-snug">{{ $st['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Active Action Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- 1. Card Psikotes -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 flex flex-col justify-between space-y-6">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-2xl">
                    📝
                </div>
                <h3 class="text-lg font-bold text-slate-900">Ujian Psikotes Online</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Ujian mencakup kemampuan numerik, logika & penalaran, ketelitian data, situasi kerja operasional, serta skala sikap kerja.
                </p>

                <div class="grid grid-cols-2 gap-3 pt-2 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Durasi Ujian</span>
                        <span class="font-extrabold text-slate-900 text-sm">{{ $testPackage->duration_minutes ?? 50 }} Menit</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Jumlah Soal</span>
                        <span class="font-extrabold text-slate-900 text-sm">{{ $testPackage->total_questions ?? 40 }} Soal</span>
                    </div>
                </div>
            </div>

            <div>
                @if(!$attempt || $attempt->status === 'in_progress')
                    <a href="{{ route('candidate.test.intro') }}" class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 text-center block transition">
                        {{ $attempt && $attempt->status === 'in_progress' ? 'Lanjutkan Pengerjaan Ujian &rarr;' : 'Mulai Ujian Psikotes Sekarang &rarr;' }}
                    </a>
                @else
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
                        <div>
                            <span class="font-bold">Psikotes Selesai:</span> Jawaban telah tersimpan pada sistem.
                        </div>
                        <a href="{{ route('candidate.test.result', $attempt->id) }}" class="font-bold text-emerald-900 underline underline-offset-2">
                            Lihat Status
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Card Jadwal Interview & Informasi -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 flex flex-col justify-between space-y-6">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center font-bold text-2xl">
                    🎙️
                </div>
                <h3 class="text-lg font-bold text-slate-900">Jadwal Wawancara (Interview)</h3>
                
                @if($candidate->latestInterview)
                    @php
                        $itw = $candidate->latestInterview;
                    @endphp
                    <div class="p-4 rounded-2xl bg-violet-50 border border-violet-100 space-y-2 text-xs text-violet-950">
                        <div class="flex items-center justify-between font-bold">
                            <span>Status Sesi:</span>
                            <span class="px-2 py-0.5 rounded-full {{ $itw->status === 'completed' ? 'bg-emerald-200 text-emerald-900' : 'bg-amber-200 text-amber-900' }}">
                                {{ $itw->status === 'completed' ? 'Selesai Dilaksanakan' : 'Terjadwal' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-violet-600 font-semibold block">Waktu Pelaksanaan:</span>
                            <span class="font-extrabold text-sm">{{ $itw->scheduled_at->format('l, d F Y - H:i') }} WIB</span>
                        </div>
                        <div>
                            <span class="text-violet-600 font-semibold block">Lokasi / Tautan:</span>
                            <span class="font-bold">{{ $itw->location }}</span>
                        </div>
                        @if($itw->notes)
                            <div class="pt-1 border-t border-violet-200/60">
                                <span class="text-violet-600 font-semibold block">Catatan HRD:</span>
                                <span class="text-slate-700">{{ $itw->notes }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-500 space-y-1">
                        <p class="font-bold text-slate-700">Belum Ada Jadwal Interview</p>
                        <p>Jadwal wawancara akan diterbitkan oleh tim HRD setelah kandidat menyelesaikan dan dinyatakan lulus ujian psikotes.</p>
                    </div>
                @endif
            </div>

            <!-- Petunjuk Kontak -->
            <div class="text-[11px] text-slate-400">
                Ada pertanyaan atau kendala teknis? Hubungi tim HRD di email resmi atau pesan WhatsApp terdaftar.
            </div>
        </div>
    </div>
</div>
@endsection
