@extends('layouts.hrd', [
    'title' => 'Kelola Paket Psikotes',
    'headerTitle' => 'Paket Psikotes & Seleksi',
    'headerSubtitle' => 'Atur komposisi jumlah soal, durasi waktu, passing grade, dan randomisasi'
])

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Paket Psikotes Aktif ({{ $packages->count() }})</h3>
            <p class="text-xs text-slate-500">Masing-masing posisi memiliki paket tes yang mengombinasikan soal umum & simulasi teknis</p>
        </div>
        <a href="{{ route('hrd.test-packages.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
            <span>➕</span> Buat Paket Baru
        </a>
    </div>

    <!-- Package Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($packages as $pkg)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between relative overflow-hidden group hover:border-indigo-300 transition">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $pkg->position ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-slate-100 text-slate-700' }}">
                            {{ $pkg->position ? $pkg->position->name : 'Semua Posisi' }}
                        </span>
                        @if($pkg->is_active)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        @else
                            <span class="text-[11px] font-bold text-slate-400">Nonaktif</span>
                        @endif
                    </div>

                    <div>
                        <h4 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition">{{ $pkg->name }}</h4>
                        <p class="text-xs text-slate-500 line-clamp-2 mt-1">{{ $pkg->description ?: 'Paket psikotes terstandarisasi untuk rekrutmen.' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px] font-bold uppercase">Jumlah Soal</span>
                            <span class="font-extrabold text-slate-900 text-sm">{{ $pkg->total_questions }} Soal</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px] font-bold uppercase">Durasi Pengerjaan</span>
                            <span class="font-extrabold text-slate-900 text-sm">{{ $pkg->duration_minutes }} Menit</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px] font-bold uppercase">Passing Grade</span>
                            <span class="font-extrabold text-emerald-600 text-sm">&ge; {{ $pkg->passing_grade }}</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block text-[10px] font-bold uppercase">Kandidat Tes</span>
                            <span class="font-extrabold text-indigo-600 text-sm">{{ $pkg->attempts_count }} Sesi</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 text-[11px] text-slate-500 pt-1">
                        <span class="inline-flex items-center gap-1">
                            {{ $pkg->is_randomized ? '🎲 Acak Soal' : '📋 Urutan Tetap' }}
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1">
                            {{ $pkg->is_options_randomized ? '🔀 Acak Opsi' : '🔤 Opsi Tetap' }}
                        </span>
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-medium">{{ $pkg->questions_count }} Soal Terdaftar</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('hrd.test-packages.edit', $pkg->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-indigo-600 hover:bg-indigo-50 border border-indigo-200 transition">
                            Edit
                        </a>
                        <form action="{{ route('hrd.test-packages.destroy', $pkg->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus paket psikotes ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-rose-600 hover:bg-rose-50 border border-rose-200 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-12 text-center text-xs text-slate-400 bg-white rounded-3xl border border-slate-200">
                Belum ada paket psikotes yang dibuat.
            </div>
        @endforelse
    </div>
</div>
@endsection
