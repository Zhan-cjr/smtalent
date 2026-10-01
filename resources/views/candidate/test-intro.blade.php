@extends('layouts.candidate', [
    'title' => 'Petunjuk Ujian Psikotes'
])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="text-center space-y-2 pb-6 border-b border-slate-100">
            <div class="w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-3xl mx-auto mb-2">
                ⏱️
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">{{ $package->name }}</h2>
            <p class="text-xs text-slate-500">Posisi: <strong class="text-slate-800">{{ $candidate->vacancy?->position?->name }}</strong></p>
        </div>

        <!-- Detail Parameters -->
        <div class="grid grid-cols-2 gap-4 text-center">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Jumlah Soal</span>
                <span class="text-2xl font-black text-slate-900 mt-0.5 block">{{ $package->total_questions }} Soal</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Waktu Pengerjaan</span>
                <span class="text-2xl font-black text-indigo-600 mt-0.5 block">{{ $package->duration_minutes }} Menit</span>
            </div>
        </div>

        <!-- Rules Checklist -->
        <div class="space-y-3 p-5 rounded-2xl bg-indigo-50/60 border border-indigo-100 text-xs text-indigo-950">
            <h4 class="font-extrabold uppercase tracking-wider text-indigo-900 flex items-center gap-1.5">
                <span>📋</span> Tata Tertib & Ketentuan Ujian:
            </h4>
            <ul class="space-y-2 list-disc list-inside text-slate-700 leading-relaxed">
                <li><strong>Timer Server-Side:</strong> Waktu terus berjalan meskipun browser di-refresh atau koneksi terputus.</li>
                <li><strong>Autosave Otomatis:</strong> Setiap jawaban yang Anda pilih akan langsung tersimpan otomatis ke database.</li>
                <li><strong>Navigasi Bebas:</strong> Anda dapat melompati soal, berpindah nomor menggunakan panel navigasi, dan mengubah jawaban sebelum waktu berakhir.</li>
                <li><strong>Auto-Submit:</strong> Jika waktu habis (00:00), sistem secara otomatis mengunci dan mengirimkan seluruh jawaban Anda.</li>
                <li>Pastikan koneksi internet Anda stabil dan berada di ruangan yang tenang sebelum menekan tombol mulai.</li>
            </ul>
        </div>

        <form action="{{ route('candidate.test.start') }}" method="POST" onsubmit="return confirm('Apakah Anda sudah siap memulai tes sekarang? Waktu pengerjaan akan langsung berjalan.');">
            @csrf
            <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                Saya Siap, Mulai Ujian Psikotes Sekarang 🚀
            </button>
        </form>
    </div>
</div>
@endsection
