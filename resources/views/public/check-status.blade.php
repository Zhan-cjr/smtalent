<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Cek Status Lamaran - SM Talent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col antialiased text-slate-800 bg-slate-50/50">
    <!-- Header (Mobile-responsive) -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 sm:py-0 min-h-16 sm:h-20 flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-bold text-lg sm:text-xl shadow-md shadow-indigo-500/20 shrink-0">
                    ⚡
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <h1 class="font-extrabold text-slate-900 text-base sm:text-lg tracking-tight leading-none">
                            SM <span class="text-indigo-600">Talent</span>
                        </h1>
                        <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" 
                           target="_blank" rel="noopener noreferrer" 
                           class="text-[10px] text-indigo-500 hover:text-indigo-700 font-bold hover:underline transition">
                            by ZhanSoft
                        </a>
                    </div>
                    <span class="text-[10px] sm:text-xs text-slate-400 font-medium block truncate">Portal Rekrutmen Online</span>
                </div>
            </a>
            <a href="{{ route('home') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 shrink-0 flex items-center gap-1">
                <span>&larr;</span> <span class="hidden sm:inline">Beranda </span>Lowongan
            </a>
        </div>
    </header>

    <main class="flex-1 max-w-2xl w-full mx-auto px-4 py-8 sm:py-12 space-y-6 sm:space-y-8">
        <!-- Search Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5 sm:p-8 space-y-4">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-slate-900">Pelacakan Status Seleksi</h2>
                <p class="text-xs text-slate-500">Masukkan Alamat Email atau Nomor WhatsApp yang Anda gunakan saat mengikuti tes</p>
            </div>

            <form action="{{ route('public.check-status') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
                <input type="text" name="q" value="{{ $query }}" required placeholder="Contoh: nama@email.com atau 08123456789"
                       class="flex-1 px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 text-base sm:text-sm font-medium">
                <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-600/20 transition text-center">
                    Cari Status
                </button>
            </form>
        </div>

        <!-- Result Card -->
        @if($candidate)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-5 sm:p-8 space-y-5 sm:space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">{{ $candidate->name }}</h3>
                        <p class="text-xs text-slate-500 break-all">{{ $candidate->email }} &bull; {{ $candidate->phone }}</p>
                    </div>
                    <span class="self-start sm:self-center px-3 py-1 rounded-full text-xs font-bold 
                        @if($candidate->status === 'DITERIMA' || $candidate->final_status === 'LOLOS') bg-emerald-100 text-emerald-800
                        @elseif($candidate->status === 'CADANGAN' || $candidate->final_status === 'CADANGAN') bg-amber-100 text-amber-800
                        @elseif($candidate->status === 'TIDAK_LULUS' || $candidate->final_status === 'TIDAK_LOLOS') bg-rose-100 text-rose-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ str_replace('_', ' ', $candidate->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block font-semibold">Posisi Dilamar</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $candidate->vacancy?->position?->name }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 block font-semibold">Status Psikotes</span>
                        <span class="font-bold text-slate-800 text-sm">
                            @if($candidate->final_psychotest_score !== null)
                                Sudah Selesai (Skor: {{ $candidate->final_psychotest_score }})
                            @else
                                Belum Mengerjakan Ujian
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Info Jadwal Tes Psikotes (Jika Belum Selesai Tes) -->
                @if($candidate->final_psychotest_score === null && isset($testScheduleInfo))
                    <div class="p-4 sm:p-5 rounded-2xl {{ $testScheduleInfo['status'] === 'ACTIVE' ? 'bg-emerald-50 border border-emerald-200 text-emerald-950' : ($testScheduleInfo['status'] === 'UPCOMING' ? 'bg-amber-50 border border-amber-200 text-amber-950' : 'bg-slate-50 border border-slate-200 text-slate-700') }} space-y-3 text-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                            <h4 class="font-extrabold flex items-center gap-1.5 text-sm">
                                <span>⏰</span> Jadwal Ujian Psikotes:
                            </h4>
                            <span class="self-start sm:self-center px-2.5 py-0.5 rounded-full text-[11px] font-bold
                                @if($testScheduleInfo['status'] === 'ACTIVE') bg-emerald-100 text-emerald-800
                                @elseif($testScheduleInfo['status'] === 'UPCOMING') bg-amber-100 text-amber-800
                                @elseif($testScheduleInfo['status'] === 'NO_SCHEDULE') bg-rose-100 text-rose-800
                                @else bg-slate-200 text-slate-700 @endif">
                                @if($testScheduleInfo['status'] === 'ACTIVE') Sedang Berlangsung (Buka)
                                @elseif($testScheduleInfo['status'] === 'UPCOMING') Belum Dimulai
                                @elseif($testScheduleInfo['status'] === 'NO_SCHEDULE') Jadwal Belum Dibuat
                                @else Telah Berakhir @endif
                            </span>
                        </div>

                        @if($testScheduleInfo['status'] === 'ACTIVE')
                            <p class="font-medium text-emerald-800">
                                Sesi ujian <strong>{{ $testScheduleInfo['active_schedule']->name }}</strong> sedang berlangsung hingga <strong>{{ $testScheduleInfo['active_schedule']->end_time->format('H:i') }} WIB</strong>.
                            </p>
                            <a href="{{ route('public.test.screen', ['token' => $candidate->access_token]) }}"
                               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition">
                                <span>📝</span> Masuk & Mulai Mengerjakan Ujian Sekarang
                            </a>
                        @elseif($testScheduleInfo['status'] === 'UPCOMING')
                            <p class="font-medium text-amber-800">
                                Sesi ujian <strong>{{ $testScheduleInfo['upcoming_schedule']->name }}</strong> akan dibuka pada:
                                <strong class="block text-slate-900 mt-1">{{ $testScheduleInfo['upcoming_schedule']->start_time->format('l, d F Y - H:i') }} WIB</strong>
                            </p>
                            <span class="text-amber-700 text-[11px]">Tombol ujian hanya bisa diklik saat jadwal telah dibuka.</span>
                        @elseif($testScheduleInfo['status'] === 'NO_SCHEDULE')
                            <p class="font-medium text-slate-600">
                                🔒 Jadwal ujian psikotes untuk posisi ini <strong>belum dibuat oleh tim HRD</strong>. Tidak ada tes yang dapat dikerjakan saat ini.
                            </p>
                            <span class="text-slate-400 text-[11px]">Silakan pantau status Anda secara berkala.</span>
                        @else
                            <p class="font-medium text-slate-500">
                                ❌ Jadwal ujian psikotes untuk sesi ini telah berakhir pada {{ $testScheduleInfo['latest_schedule']?->end_time?->format('d M Y - H:i') }} WIB.
                            </p>
                        @endif
                    </div>
                @endif

                <!-- Sesi Wawancara (Interview) -->
                @if($candidate->latestInterview)
                    <div class="p-4 sm:p-5 rounded-2xl bg-violet-50 border border-violet-100 space-y-2 text-xs text-violet-950">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                            <h4 class="font-extrabold text-violet-900 flex items-center gap-1.5 text-sm">
                                <span>🎙️</span> Undangan Wawancara (Interview):
                            </h4>
                            <span class="self-start sm:self-center px-2.5 py-0.5 rounded-full text-[11px] font-bold
                                @if($candidate->latestInterview->status === 'completed') bg-emerald-100 text-emerald-800
                                @elseif($candidate->latestInterview->status === 'in_progress') bg-indigo-100 text-indigo-800
                                @else bg-violet-100 text-violet-800 @endif">
                                {{ $candidate->latestInterview->status === 'completed' ? 'Selesai Dinilai' : ($candidate->latestInterview->status === 'in_progress' ? 'Sedang Berlangsung' : 'Terjadwal') }}
                            </span>
                        </div>
                        <p>Waktu Sesi: <strong class="text-slate-900">{{ $candidate->latestInterview->scheduled_at->format('l, d F Y - H:i') }} WIB</strong></p>
                        <p>Lokasi / Ruang: <strong class="text-slate-900">{{ $candidate->latestInterview->location }}</strong></p>
                        @if($candidate->latestInterview->interviewer)
                            <p class="text-slate-600">Pewawancara: <strong>{{ $candidate->latestInterview->interviewer->name }}</strong></p>
                        @endif
                        @if($candidate->latestInterview->notes)
                            <p class="text-slate-600">Instruksi: {{ $candidate->latestInterview->notes }}</p>
                        @endif
                    </div>
                @elseif(in_array($candidate->status, ['LULUS_PSIKOTES', 'INTERVIEW']))
                    <!-- Sudah lolos psikotes tapi belum dijadwalkan wawancara -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 space-y-1.5 text-xs text-amber-950">
                        <h4 class="font-extrabold text-amber-900 flex items-center gap-1.5 text-sm">
                            <span>⏳</span> Status Jadwal Wawancara (Interview):
                        </h4>
                        <p class="font-medium text-amber-800">
                            Selamat! Anda dinyatakan <strong>LULUS PSIKOTES</strong>. Saat ini jadwal sesi wawancara <strong>belum dibuat oleh tim HRD</strong>.
                        </p>
                        <p class="text-amber-700/80 text-[11px]">
                            Harap periksa halaman ini secara berkala atau tunggu konfirmasi jadwal resmi dari HRD.
                        </p>
                    </div>
                @endif
            </div>
        @elseif($query)
            <div class="p-8 text-center bg-white rounded-3xl border border-slate-200 shadow-xs space-y-2">
                <span class="text-3xl block">🔍</span>
                <h3 class="text-base font-bold text-slate-900">Data Tidak Ditemukan</h3>
                <p class="text-xs text-slate-400">Tidak ada data pendaftaran psikotes dengan kontak "<strong>{{ $query }}</strong>". Silakan periksa kembali penulisan email atau nomor HP Anda.</p>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-4xl mx-auto px-4 space-y-1">
            <p>&copy; {{ date('Y') }} <strong>SM Talent</strong> &bull; Sistem Rekrutmen & Asesmen Online.</p>
            <p class="text-[11px] text-slate-400">
                Created with excellence by 
                <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" 
                   target="_blank" rel="noopener noreferrer" 
                   class="font-bold text-indigo-600 hover:text-indigo-700 hover:underline inline-flex items-center gap-0.5">
                    <span>ZhanSoft</span>
                    <span>↗</span>
                </a>
            </p>
        </div>
    </footer>
</body>
</html>
