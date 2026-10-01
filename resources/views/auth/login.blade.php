<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Login HRD / Admin - SM Talent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-slate-200">
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-6 sm:mb-8">
            <div class="inline-flex w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 items-center justify-center text-white text-2xl sm:text-3xl font-extrabold shadow-xl shadow-indigo-500/30 mb-3 sm:mb-4 ring-4 ring-indigo-500/20">
                ⚡
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                SM <span class="text-indigo-400">Talent</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1 flex items-center justify-center gap-1">
                <span>Portal HRD & Administrator</span>
                <span>&bull;</span>
                <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" 
                   target="_blank" rel="noopener noreferrer" 
                   class="text-indigo-400 hover:text-indigo-300 font-bold hover:underline transition">
                    by ZhanSoft
                </a>
            </p>
        </div>

        <!-- Card Container -->
        <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800/80 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-black/40">
            @if(session('error'))
                <div class="mb-5 p-3.5 sm:p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-semibold flex items-center gap-2">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-3.5 sm:p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-semibold flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 sm:p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Email HRD / Admin</label>
                    <input type="email" name="email" id="emailInput" value="{{ old('email', 'hrd@example.com') }}" required 
                           class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-base sm:text-sm font-medium transition"
                           placeholder="hrd@example.com">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Password</label>
                    <input type="password" name="password" id="passwordInput" value="Password123!" required 
                           class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-base sm:text-sm font-medium transition"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs text-slate-400 font-medium">Ingat Sesi Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition duration-200 mt-2">
                    Masuk ke Dashboard HRD
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-800 text-center">
                <a href="{{ route('home') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition">
                    &larr; Kembali ke Beranda Lowongan Publik
                </a>
            </div>
        </div>

        <p class="text-center text-[11px] text-slate-500 mt-6">
            &copy; {{ date('Y') }} <strong>SM Talent</strong> &bull; 
            <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" 
               target="_blank" rel="noopener noreferrer" 
               class="text-indigo-400 hover:text-indigo-300 font-semibold hover:underline">by ZhanSoft</a>
        </p>
    </div>
</body>
</html>
