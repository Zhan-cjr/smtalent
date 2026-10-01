@extends('layouts.hrd', [
    'title' => 'Bank Soal Psikotes',
    'headerTitle' => 'Bank Soal & Skala Sikap Kerja',
    'headerSubtitle' => 'Kelola soal pilihan ganda, skala Likert 1-5, filter kategori dan ekspor Excel'
])

@section('content')
<div class="space-y-6">
    <!-- Top Action & Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Koleksi Bank Soal (Total: {{ $questions->total() }})</h3>
                <p class="text-xs text-slate-500">Mencakup soal numerik, logika, verbal, ketelitian, situasi kerja, dan perilaku kerja</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('hrd.questions.export') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                    <span>📊</span> Export Excel
                </a>
                <a href="{{ route('hrd.questions.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                    <span>➕</span> Tambah Soal Baru
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('hrd.questions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 border-t border-slate-100">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Kategori</label>
                <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Posisi Jabatan</label>
                <select name="position_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Posisi</option>
                    <option value="general" {{ $positionId === 'general' ? 'selected' : '' }}>Umum (Semua Posisi)</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ $positionId == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Tipe Soal</label>
                <select name="type" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    <option value="multiple_choice" {{ $type === 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda (A-D)</option>
                    <option value="likert_scale" {{ $type === 'likert_scale' ? 'selected' : '' }}>Skala Sikap (1-5)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Cari Soal</label>
                <div class="flex items-center gap-1.5">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500">
                    <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">Cari</button>
                    @if($categoryId || $positionId || $type || $search)
                        <a href="{{ route('hrd.questions.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold" title="Reset">✕</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Questions Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3 px-5 text-center w-12">#</th>
                        <th class="py-3 px-5">Pertanyaan & Pilihan Jawaban</th>
                        <th class="py-3 px-4">Kategori & Aspek</th>
                        <th class="py-3 px-4">Posisi</th>
                        <th class="py-3 px-4 text-center">Tipe</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($questions as $index => $q)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-5 text-center font-bold text-xs text-slate-400">
                                {{ $questions->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-5 max-w-lg">
                                <p class="font-bold text-slate-900 line-clamp-2">{{ $q->question_text }}</p>
                                @if($q->type === 'multiple_choice')
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                        @foreach($q->options as $opt)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md {{ $opt->is_correct ? 'bg-emerald-100 text-emerald-800 font-bold border border-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                                <span class="font-mono font-bold">{{ $opt->option_key }}.</span> {{ \Illuminate\Support\Str::limit($opt->option_text, 25) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="inline-block mt-1 text-xs text-violet-600 font-semibold bg-violet-50 px-2 py-0.5 rounded">
                                        ⭐ Skala 1 (Sangat Tidak Sesuai) s/d 5 (Sangat Sesuai)
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="block text-xs font-semibold text-slate-800">{{ $q->category?->name }}</span>
                                @if($q->aspect)
                                    <span class="inline-block mt-0.5 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">{{ $q->aspect }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-xs font-medium text-slate-600">
                                {{ $q->position?->name ?? 'Semua Posisi (Umum)' }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $q->type === 'multiple_choice' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $q->type === 'multiple_choice' ? 'PG' : 'Likert 1-5' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($q->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right space-x-1 whitespace-nowrap">
                                <form action="{{ route('hrd.questions.duplicate', $q->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-600 hover:bg-slate-100 border border-slate-200" title="Duplikasi">
                                        Duplikat
                                    </button>
                                </form>
                                <a href="{{ route('hrd.questions.edit', $q->id) }}" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg text-indigo-600 hover:bg-indigo-50 border border-indigo-200">
                                    Edit
                                </a>
                                <form action="{{ route('hrd.questions.destroy', $q->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus soal ini dari Bank Soal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg text-rose-600 hover:bg-rose-50 border border-rose-200">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-slate-400 font-medium">Tidak ada soal yang sesuai filter pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($questions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $questions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
