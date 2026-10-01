<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Asesmen Selesai - SM Talent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col items-center justify-between p-4 bg-slate-50">
    <!-- Header Mini -->
    <div class="py-4 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-bold text-base shadow-sm">⚡</span>
            <span class="font-extrabold text-slate-900 text-lg tracking-tight">SM <span class="text-indigo-600">Talent</span></span>
        </a>
        <div class="mt-0.5">
            <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" 
               target="_blank" rel="noopener noreferrer" 
               class="text-[11px] text-indigo-500 hover:text-indigo-700 font-bold hover:underline transition">
                by ZhanSoft
            </a>
        </div>
    </div>

    <div class="max-w-xl w-full bg-white rounded-3xl border border-slate-200 shadow-xl p-6 sm:p-10 text-center space-y-6 my-auto">
        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-3xl mx-auto shadow-sm">
            ✅
        </div>

        <div class="space-y-1">
            <h2 class="text-xl sm:text-2xl font-black text-slate-900">Ujian Psikotes Berhasil Diselesaikan!</h2>
            <p class="text-xs text-slate-500 leading-relaxed">
                Terima kasih, <strong class="text-slate-800">{{ $candidate->name }}</strong>. Lembar jawaban Anda telah tersimpan dan terhitung secara otomatis ke dalam sistem seleksi.
            </p>
        </div>

        <!-- Info Card -->
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs text-left">
            <div>
                <span class="text-slate-400 block font-semibold">Posisi Dilamar:</span>
                <span class="font-bold text-slate-800">{{ $candidate->vacancy?->position?->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">Waktu Submit:</span>
                <span class="font-bold text-slate-800">{{ $attempt->submitted_at?->format('d M Y - H:i') }} WIB</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">Total Soal Terjawab:</span>
                <span class="font-bold text-slate-800">{{ $attempt->total_answered }} / {{ $attempt->total_questions }} Soal</span>
            </div>
            <div>
                <span class="text-slate-400 block font-semibold">Status Pengerjaan:</span>
                <span class="font-bold text-emerald-600">Selesai (Submitted)</span>
            </div>
        </div>

        @if($package->show_result_to_candidate)
            <!-- Jika HRD Mengizinkan Nilai Ditampilkan -->
            <div class="p-5 sm:p-6 rounded-2xl bg-indigo-50 border border-indigo-100 space-y-1">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-900">Skor Psikotes Anda</span>
                <div class="text-3xl sm:text-4xl font-black text-indigo-600">{{ $attempt->score_multiple_choice }} / 100</div>
                <p class="text-xs font-semibold {{ $attempt->is_passed ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $attempt->is_passed ? '✓ LULUS Passing Grade (' . $package->passing_grade . ')' : '✕ Belum Memenuhi Passing Grade (' . $package->passing_grade . ')' }}
                </p>
            </div>
        @else
            <!-- Notice Standar Rekrutmen -->
            <div class="p-4 sm:p-5 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-xs text-indigo-950 text-left space-y-2">
                <p class="font-bold flex items-center gap-1.5"><span>📢</span> Tahapan Selanjutnya:</p>
                <p class="leading-relaxed text-slate-600">
                    Tim HRD akan merekap ranking seluruh kandidat. Jika Anda memenuhi kriteria dan passing grade, tim rekrutmen akan menghubungi Anda melalui WhatsApp di nomor <strong>{{ $candidate->phone }}</strong> untuk jadwal wawancara kerja.
                </p>
            </div>
        @endif

        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-center gap-2.5 sm:gap-3">
            <a href="{{ route('public.check-status', ['q' => $candidate->email]) }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                🔍 Cek Status Lamaran
            </a>
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition">
                Kembali ke Beranda Utama
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-4 text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} <strong>SM Talent</strong> &bull; Developed by <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" target="_blank" rel="noopener noreferrer" class="text-indigo-600 font-bold hover:underline">ZhanSoft</a></p>
    </footer>
</body>
</html>
