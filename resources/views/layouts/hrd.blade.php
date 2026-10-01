<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard HRD' }} - SM Talent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full antialiased text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        <!-- Sidebar Backdrop for Mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden" style="display: none;"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col shadow-2xl">
            <!-- Brand -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-950/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/30 shrink-0">
                        ⚡
                    </div>
                    <div>
                        <h1 class="font-extrabold text-white text-base tracking-tight leading-none">SM <span class="text-indigo-400">Talent</span></h1>
                        <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" 
                           target="_blank" rel="noopener noreferrer" 
                           class="text-[10px] text-slate-400 hover:text-indigo-300 transition block mt-0.5 font-medium">
                            by <span class="text-indigo-400 font-semibold hover:underline">ZhanSoft</span>
                        </a>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 text-xl leading-none">&times;</button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 custom-scrollbar">
                <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Main Menu</div>
                
                <a href="{{ route('hrd.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">📊</span>
                    <span>Dashboard HRD</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Master & Lowongan</div>

                <a href="{{ route('hrd.positions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.positions.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">🏢</span>
                    <span>Posisi / Jabatan</span>
                </a>

                <a href="{{ route('hrd.vacancies.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.vacancies.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">📢</span>
                    <span>Lowongan Rekrutmen</span>
                </a>

                <a href="{{ route('hrd.candidates.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.candidates.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">👥</span>
                    <span>Semua Kandidat</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Bank Soal & Psikotes</div>

                <a href="{{ route('hrd.questions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.questions.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">📚</span>
                    <span>Bank Soal</span>
                </a>

                <a href="{{ route('hrd.test-packages.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.test-packages.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">📦</span>
                    <span>Paket Psikotes</span>
                </a>

                <a href="{{ route('hrd.test-schedules.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.test-schedules.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">⏰</span>
                    <span>Jadwal Psikotes</span>
                </a>

                <a href="{{ route('hrd.rankings.psychotest') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.rankings.psychotest') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">🎯</span>
                    <span>Hasil & Ranking Psikotes</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Wawancara & Keputusan</div>

                <a href="{{ route('hrd.interviews.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.interviews.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">🎙️</span>
                    <span>Jadwal & Nilai Interview</span>
                </a>

                <a href="{{ route('hrd.rankings.final') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.rankings.final') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">🏆</span>
                    <span>Ranking Akhir (60:40)</span>
                </a>

                <div class="pt-4 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Laporan & Pengaturan</div>

                <a href="{{ route('hrd.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.reports.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">📄</span>
                    <span>Export Excel & PDF</span>
                </a>

                <a href="{{ route('hrd.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.settings.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">⚙️</span>
                    <span>Bobot & Pengaturan</span>
                </a>

                <a href="{{ route('hrd.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">👤</span>
                    <span>Kelola Pengguna</span>
                </a>

                <a href="{{ route('hrd.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('hrd.audit-logs.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <span class="text-lg">🛡️</span>
                    <span>Audit Log</span>
                </a>
            </div>

            <!-- User Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/60">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center font-bold text-sm shrink-0">
                            HR
                        </div>
                        <div class="truncate">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header (Responsive) -->
            <header class="min-h-16 sm:h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-30 sticky top-0 py-2.5 sm:py-0 gap-3">
                <div class="flex items-center gap-2.5 sm:gap-4 min-w-0">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 border border-slate-200 shrink-0" aria-label="Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-xl font-bold text-slate-900 leading-tight truncate">{{ $headerTitle ?? 'Dashboard' }}</h2>
                        <p class="text-[11px] sm:text-xs text-slate-500 truncate hidden sm:block">{{ $headerSubtitle ?? 'Kelola proses rekrutmen & seleksi secara objektif' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="hidden sm:inline">Sistem </span>Aktif
                    </span>
                    <a href="{{ route('hrd.rankings.final') }}" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 sm:px-4 sm:py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition">
                        <span>🏆 Ranking Akhir</span>
                    </a>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
                <!-- Alerts / Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 text-emerald-800 shadow-sm" x-data="{ show: true }" x-show="show">
                        <span class="text-xl">✅</span>
                        <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
                        <button @click="show = false" class="text-emerald-600 hover:text-emerald-900 text-lg leading-none">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3 text-rose-800 shadow-sm" x-data="{ show: true }" x-show="show">
                        <span class="text-xl">⚠️</span>
                        <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
                        <button @click="show = false" class="text-rose-600 hover:text-rose-900 text-lg leading-none">&times;</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                        <div class="flex items-center gap-2 font-bold text-sm mb-1">
                            <span>⚠️</span> Terdapat beberapa kesalahan input:
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
