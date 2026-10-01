<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Kandidat - Sistem Psikotes & Seleksi Karyawan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-slate-200 py-12">
    <div class="w-full max-w-xl">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 items-center justify-center text-white text-2xl font-extrabold shadow-xl shadow-indigo-500/30 mb-3">
                🎓
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Formulir Pendaftaran Kandidat</h1>
            <p class="text-xs text-slate-400 mt-1">Lengkapi data diri untuk mengikuti rangkaian seleksi psikotes & wawancara</p>
        </div>

        <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800/80 rounded-3xl p-8 shadow-2xl shadow-black/40">
            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Pilih Lowongan Pekerjaan</label>
                    <select name="vacancy_id" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium transition">
                        <option value="">-- Pilih Posisi / Lowongan --</option>
                        @foreach($vacancies as $vac)
                            <option value="{{ $vac->id }}" {{ old('vacancy_id') == $vac->id ? 'selected' : '' }}>
                                {{ $vac->position->name }} ({{ $vac->title }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 text-sm font-medium transition"
                               placeholder="Nama Lengkap Anda">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                               class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 text-sm font-medium transition"
                               placeholder="email@domain.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">No. WhatsApp / HP</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required 
                               class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 text-sm font-medium transition"
                               placeholder="08123456789">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Jenis Kelamin</label>
                        <select name="gender" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm font-medium transition">
                            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Password</label>
                        <input type="password" name="password" required 
                               class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 text-sm font-medium transition"
                               placeholder="Minimal 8 karakter">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required 
                               class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 text-sm font-medium transition"
                               placeholder="Ulangi password">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition duration-200 mt-2">
                    Daftar & Lanjut ke Portal Ujian
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-xs text-slate-400">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="font-bold text-indigo-400 hover:text-indigo-300 transition underline underline-offset-4">Masuk ke Sini</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
