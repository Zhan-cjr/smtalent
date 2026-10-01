<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal Kandidat' }} - SM Talent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col antialiased text-slate-800 bg-slate-50/50">
    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-indigo-500/30">
                            ⚡
                        </div>
                        <div>
                            <span class="font-extrabold text-slate-900 text-lg tracking-tight">SM <span class="text-indigo-600">Talent</span></span>
                            <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" 
                               target="_blank" rel="noopener noreferrer" 
                               class="block text-[10px] text-indigo-500 hover:text-indigo-700 font-bold hover:underline transition">
                                by ZhanSoft
                            </a>
                        </div>
                    </a>

                    <div class="hidden md:flex items-center gap-2">
                        <a href="{{ route('candidate.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('candidate.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Dashboard & Jadwal
                        </a>
                        <a href="{{ route('candidate.profile') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('candidate.profile') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Profil Saya
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-3 text-right">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 font-medium">{{ auth()->user()->candidate?->vacancy?->position?->name ?? 'Kandidat Pelamar' }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 border border-indigo-200 flex items-center justify-center font-bold text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 border border-rose-200 rounded-xl transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} <strong>SM Talent</strong> &bull; Developed by <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" target="_blank" rel="noopener noreferrer" class="text-indigo-600 font-bold hover:underline">ZhanSoft</a></p>
    </footer>
    @stack('scripts')
</body>
</html>
