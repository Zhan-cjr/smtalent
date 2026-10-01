@extends('layouts.hrd', [
    'title' => 'Tambah Soal Baru',
    'headerTitle' => 'Tambah Soal Bank Soal',
    'headerSubtitle' => 'Buat soal pilihan ganda atau pertanyaan skala sikap kerja'
])

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8" x-data="{ qType: 'multiple_choice' }">
    <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Formulir Soal Psikotes</h3>
            <p class="text-xs text-slate-500">Tentukan kategori, sasaran posisi, dan kunci jawaban</p>
        </div>
        <a href="{{ route('hrd.questions.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
            &larr; Kembali ke Bank Soal
        </a>
    </div>

    <form action="{{ route('hrd.questions.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tipe Soal</label>
                <select name="type" x-model="qType" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-semibold">
                    <option value="multiple_choice">Pilihan Ganda (A - D)</option>
                    <option value="likert_scale">Skala Sikap / Perilaku Kerja (1 - 5)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kategori Soal</label>
                <select name="category_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Khusus Posisi (Opsional)</label>
                <select name="position_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    <option value="">Semua Posisi (Soal Umum)</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Aspek / Dimensi Evaluasi</label>
                <input type="text" name="aspect" placeholder="Contoh: Ketelitian, Disiplin, Keramahan"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Teks Soal / Pertanyaan / Studi Kasus</label>
            <textarea name="question_text" rows="4" required placeholder="Tuliskan pertanyaan secara jelas dan tidak ambigu..."
                      class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium"></textarea>
        </div>

        <!-- Section Opsi Pilihan Ganda -->
        <div x-show="qType === 'multiple_choice'" class="space-y-3 p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Pilihan Jawaban & Kunci Benar</h4>
            
            <div class="space-y-3">
                @foreach(['A', 'B', 'C', 'D'] as $key)
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer shrink-0">
                            <input type="radio" name="correct_option" value="{{ $key }}" {{ $key === 'A' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600">
                            <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-mono font-bold text-xs">{{ $key }}</span>
                        </label>
                        <input type="text" name="options[{{ $key }}]" placeholder="Teks Pilihan Jawaban {{ $key }}..."
                               class="flex-1 px-4 py-2 rounded-xl bg-white border border-slate-300 text-sm font-medium focus:border-indigo-500">
                    </div>
                @endforeach
            </div>
            <p class="text-[11px] text-slate-500 mt-2">* Pilih tombol bulat pada opsi yang merupakan kunci jawaban yang benar.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Bobot Nilai Soal</label>
                <input type="number" step="0.1" name="weight" value="1.0" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
            </div>

            <div class="flex items-center gap-2 pt-6">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 rounded text-indigo-600 border-slate-300">
                <label for="is_active" class="text-xs font-bold text-slate-700">Status Soal Aktif</label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="{{ route('hrd.questions.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                Simpan Soal ke Bank Soal
            </button>
        </div>
    </form>
</div>
@endsection
