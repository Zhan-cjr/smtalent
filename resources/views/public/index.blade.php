<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>SM Talent - Portal Seleksi & Rekrutmen Karyawan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col antialiased text-slate-800 bg-slate-50/50" 
      x-data="{
          modalOpen: false,
          selectedVacancy: null,
          openModal(vac) {
              this.selectedVacancy = vac;
              this.modalOpen = true;
          }
      }">

    <!-- Top Navigation Bar (Mobile-friendly) -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-0 sm:h-20 flex items-center justify-between gap-2">
            <!-- Brand -->
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
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
                    <span class="text-[10px] sm:text-xs text-slate-400 font-medium block truncate">Portal Rekrutmen & Asesmen Online</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                <a href="{{ route('public.check-status') }}" 
                   class="px-2.5 py-1.5 sm:px-4 sm:py-2 text-[11px] sm:text-xs font-bold text-slate-700 hover:bg-slate-100 rounded-xl border border-slate-200 sm:border-transparent transition flex items-center gap-1">
                    <span>🔍</span> <span class="hidden sm:inline">Cek </span>Status
                </a>
                <a href="{{ route('login') }}" 
                   class="px-3 py-1.5 sm:px-4 sm:py-2 text-[11px] sm:text-xs font-bold text-indigo-600 hover:bg-indigo-50 border border-indigo-200 rounded-xl transition flex items-center gap-1">
                    <span>👔</span> <span>Login HRD</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-indigo-950 via-slate-900 to-slate-900 text-white py-10 sm:py-16 px-4 sm:px-6 lg:px-8 text-center relative overflow-hidden">
        <div class="max-w-3xl mx-auto space-y-3 sm:space-y-4 relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] sm:text-xs font-extrabold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 shadow-inner">
                <span>🚀</span> Seleksi Cepat Tanpa Perlu Daftar Akun
            </span>
            <h2 class="text-2xl sm:text-4xl md:text-5xl font-black tracking-tight leading-tight">
                Ikuti Psikotes & Seleksi Karyawan SM Talent
            </h2>
            <p class="text-xs sm:text-base text-indigo-200/90 max-w-2xl mx-auto leading-relaxed">
                Pilih posisi pekerjaan yang Anda minati, lengkapi identitas singkat, dan langsung kerjakan ujian psikotes secara objektif dan transparan.
            </p>
        </div>
    </section>

    <!-- Vacancies Grid Section -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-6 sm:space-y-8">
        @if(session('error'))
            <div class="p-3.5 sm:p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-start gap-2.5 sm:gap-3 shadow-sm">
                <span class="text-lg sm:text-xl">⚠️</span>
                <div class="flex-1 font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('success'))
            <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-start gap-2.5 sm:gap-3 shadow-sm">
                <span class="text-lg sm:text-xl">✅</span>
                <div class="flex-1 font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-4 pb-3 sm:pb-4 border-b border-slate-200">
            <div>
                <h3 class="text-lg sm:text-xl font-black text-slate-900">Lowongan Pekerjaan Tersedia</h3>
                <p class="text-xs text-slate-500">Ujian psikotes hanya dapat dikerjakan saat jadwal sesi tes dibuka oleh HRD</p>
            </div>
            <span class="self-start sm:self-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                {{ $vacancies->count() }} Posisi Buka
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @forelse($vacancies as $vac)
                <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-xs hover:shadow-xl hover:border-indigo-300 transition duration-300 flex flex-col justify-between space-y-5 sm:space-y-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 truncate">
                                {{ $vac->position->name }}
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 shrink-0">
                                Kuota: <strong class="text-slate-700">{{ $vac->quota }} Orang</strong>
                            </span>
                        </div>

                        <div>
                            <h4 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">{{ $vac->title }}</h4>
                            <p class="text-xs text-slate-500 line-clamp-3 mt-1.5 leading-relaxed">
                                {{ $vac->description ?: $vac->position->description }}
                            </p>
                        </div>

                        <div class="pt-2 text-[11px] text-slate-500 space-y-1 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                            <p>⏱️ Durasi: <strong>{{ $vac->testPackage?->duration_minutes ?? 50 }} Menit</strong> ({{ $vac->testPackage?->total_questions ?? 40 }} Soal)</p>
                            
                            <!-- Status Jadwal Tes -->
                            @if($vac->scheduleStatus === 'NO_SCHEDULE')
                                <p class="text-rose-600 font-bold flex items-center gap-1">
                                    <span>🔒</span> Jadwal tes: <span class="underline">Belum dibuat oleh HRD</span>
                                </p>
                            @elseif($vac->scheduleStatus === 'ACTIVE')
                                <p class="text-emerald-700 font-bold flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                                    <span>Sesi Tes: <strong>{{ $vac->activeSchedule->name }}</strong> (s/d {{ $vac->activeSchedule->end_time->format('H:i') }} WIB)</span>
                                </p>
                            @elseif($vac->scheduleStatus === 'UPCOMING')
                                <p class="text-amber-700 font-semibold flex items-center gap-1">
                                    <span>⏳</span> Jadwal buka: {{ $vac->upcomingSchedule->start_time->format('d M Y - H:i') }} WIB
                                </p>
                            @else
                                <p class="text-slate-400 font-medium">
                                    ❌ Jadwal tes telah ditutup
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Aksi Sesuai Jadwal -->
                    @if($vac->scheduleStatus === 'ACTIVE')
                        <div class="space-y-1.5">
                            <button type="button" @click="openModal({{ Js::from($vac) }})"
                                    class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-extrabold text-xs shadow-md shadow-indigo-600/20 text-center transition transform active:scale-98 flex items-center justify-center gap-2">
                                <span>📝</span> Mulai Psikotes {{ $vac->position->name }}
                            </button>
                            <span class="block text-[10px] text-center text-emerald-600 font-bold">
                                🟢 Sesi dibuka &bull; Klik untuk mulai ujian
                            </span>
                        </div>
                    @elseif($vac->scheduleStatus === 'UPCOMING')
                        <div class="space-y-1.5">
                            <button type="button" disabled
                                    class="w-full py-3.5 px-4 rounded-2xl bg-amber-50 text-amber-800 border border-amber-300 font-extrabold text-xs cursor-not-allowed flex items-center justify-center gap-2 opacity-90">
                                <span>⏳</span> Tes Belum Dibuka
                            </button>
                            <span class="block text-[10px] text-center text-amber-700 font-medium">
                                Dibuka pada {{ $vac->upcomingSchedule->start_time->format('d M Y H:i') }} WIB
                            </span>
                        </div>
                    @elseif($vac->scheduleStatus === 'EXPIRED')
                        <div class="space-y-1.5">
                            <button type="button" disabled
                                    class="w-full py-3.5 px-4 rounded-2xl bg-slate-100 text-slate-400 border border-slate-200 font-bold text-xs cursor-not-allowed flex items-center justify-center gap-2">
                                <span>❌</span> Jadwal Tes Berakhir
                            </button>
                            <span class="block text-[10px] text-center text-slate-400">
                                Sesi ujian telah ditutup
                            </span>
                        </div>
                    @else
                        <!-- NO_SCHEDULE: Belum ada jadwal yang dibuat HRD -->
                        <div class="space-y-1.5">
                            <button type="button" disabled
                                    class="w-full py-3.5 px-4 rounded-2xl bg-slate-200 text-slate-400 border border-slate-300 font-extrabold text-xs cursor-not-allowed flex items-center justify-center gap-2">
                                <span>🔒</span> Jadwal Tes Belum Dibuat
                            </button>
                            <span class="block text-[10px] text-center text-slate-400 font-medium">
                                Tidak dapat diklik karena jadwal tes belum ditentukan oleh HRD
                            </span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 py-12 text-center text-xs text-slate-400 bg-white rounded-3xl border border-slate-200">
                    Saat ini belum ada lowongan rekrutmen yang dibuka.
                </div>
            @endforelse
        </div>
    </main>

    <!-- Modal Form Identitas Langsung Masuk Tes (Tanpa Password, Responsive) -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/80 backdrop-blur-md" style="display: none;">
        <div @click.away="modalOpen = false" class="bg-white rounded-3xl p-5 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 sm:pb-4 border-b border-slate-100 mb-4 sm:mb-5">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">Identitas Peserta Tes</h3>
                    <p class="text-xs text-slate-500">Posisi: <strong class="text-indigo-600" x-text="selectedVacancy ? selectedVacancy.title : ''"></strong></p>
                </div>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none p-1">&times;</button>
            </div>

            <form action="{{ route('public.test.start') }}" method="POST" class="space-y-3.5 sm:space-y-4">
                @csrf
                <input type="hidden" name="vacancy_id" :value="selectedVacancy ? selectedVacancy.id : ''">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Sesuai KTP / Ijazah"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Alamat Email Aktif</label>
                        <input type="email" name="email" required placeholder="nama@email.com"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="phone" required placeholder="08123456789"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Jenis Kelamin</label>
                        <select name="gender" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Pendidikan Terakhir</label>
                        <input type="text" name="education" placeholder="SMA / SMK / D3 / S1..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">NIK (Nomor KTP - Opsional)</label>
                    <input type="text" name="nik" placeholder="16 digit NIK..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                </div>

                <div class="p-3 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-[11px] text-indigo-900 leading-relaxed">
                    ⏱️ <strong>Catatan:</strong> Setelah menekan tombol di bawah, Anda akan langsung diarahkan ke layar ujian. Timer tes akan langsung berjalan.
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30">
                        Masuk & Mulai Tes 🚀
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer with ZhanSoft attribution -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 space-y-1">
            <p>&copy; {{ date('Y') }} <strong>SM Talent</strong> &bull; Sistem Asesmen & Seleksi Karyawan.</p>
            <p class="text-[11px] text-slate-400">
                Created with excellence by 
                <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" 
                   target="_blank" rel="noopener noreferrer" 
                   class="font-bold text-indigo-600 hover:text-indigo-700 hover:underline inline-flex items-center gap-1">
                    <span>ZhanSoft</span>
                    <span>↗</span>
                </a>
            </p>
        </div>
    </footer>
</body>
</html>
