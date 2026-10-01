@extends('layouts.candidate', [
    'title' => 'Hasil Ujian Psikotes'
])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 text-center space-y-6">
        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-3xl mx-auto">
            ✅
        </div>

        <div class="space-y-1">
            <h2 class="text-2xl font-extrabold text-slate-900">Ujian Psikotes Berhasil Dikirim</h2>
            <p class="text-xs text-slate-500">Seluruh lembar jawaban Anda telah tersimpan dengan aman pada sistem seleksi HRD.</p>
        </div>

        <!-- Detail Paket & Waktu Submit -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block font-semibold">Paket Psikotes:</span>
                <span class="font-bold text-slate-800">{{ $package->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">Waktu Penyelesaian:</span>
                <span class="font-bold text-slate-800">{{ $attempt->submitted_at?->format('d M Y - H:i') }} WIB</span>
            </div>
        </div>

        @if($package->show_result_to_candidate)
            <!-- Jika HRD Mengaktifkan Opsi Tampilkan Nilai -->
            <div class="p-6 rounded-2xl bg-indigo-50 border border-indigo-100 space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-900">Skor Psikotes Anda</span>
                <div class="text-4xl font-black text-indigo-600">{{ $attempt->score_multiple_choice }} / 100</div>
                <p class="text-xs font-semibold {{ $attempt->is_passed ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $attempt->is_passed ? 'Status: LULUS Passing Grade (' . $package->passing_grade . ')' : 'Status: Belum Memenuhi Passing Grade (' . $package->passing_grade . ')' }}
                </p>
            </div>
        @else
            <!-- Default: Nilai dirahasiakan untuk HRD -->
            <div class="p-5 rounded-2xl bg-blue-50/60 border border-blue-100 text-xs text-blue-950 text-left space-y-2">
                <p class="font-bold flex items-center gap-1.5"><span>ℹ️</span> Tahapan Selanjutnya:</p>
                <p class="leading-relaxed text-slate-600">
                    Nilai dan performa tes Anda sedang ditinjau secara komprehensif oleh tim Talent Acquisition. 
                    Kandidat yang lolos passing grade akan dihubungi untuk undangan sesi <strong>Wawancara (Interview)</strong>. Silakan periksa dashboard atau WhatsApp Anda secara berkala.
                </p>
            </div>
        @endif

        <div class="pt-4 border-t border-slate-100">
            <a href="{{ route('candidate.dashboard') }}" class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 text-center block transition">
                Kembali ke Dashboard Kandidat &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
