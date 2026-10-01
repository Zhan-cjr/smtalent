@extends('layouts.hrd', [
    'title' => 'Buat Paket Psikotes',
    'headerTitle' => 'Buat Paket Psikotes Baru',
    'headerSubtitle' => 'Konfigurasi jumlah soal, durasi waktu, passing grade, dan pemilihan soal'
])

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
    <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Formulir Konfigurasi Paket Psikotes</h3>
            <p class="text-xs text-slate-500">Sesuaikan durasi pengerjaan dan bobot kelulusan</p>
        </div>
        <a href="{{ route('hrd.test-packages.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('hrd.test-packages.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Paket</label>
                <input type="text" name="name" required placeholder="Contoh: Paket Psikotes Pramuniaga Standar"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Target Posisi Jabatan</label>
                <select name="position_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    <option value="">Semua Posisi (Umum)</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Deskripsi / Petunjuk Tes</label>
            <textarea name="description" rows="3" placeholder="Informasi materi dan tata tertib pengerjaan..."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium"></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Jumlah Soal Ditampilkan</label>
                <input type="number" name="total_questions" value="40" min="1" required
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 focus:border-indigo-500 text-sm font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Durasi Waktu (Menit)</label>
                <input type="number" name="duration_minutes" value="50" min="1" required
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 focus:border-indigo-500 text-sm font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Passing Grade (0 - 100)</label>
                <input type="number" step="0.1" name="passing_grade" value="70.0" min="0" max="100" required
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 focus:border-indigo-500 text-sm font-bold text-emerald-600">
            </div>
        </div>

        <div class="space-y-3 p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Pengaturan Acak & Visibilitas</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_randomized" value="1" checked class="w-4 h-4 rounded text-indigo-600">
                    <span class="text-xs font-medium text-slate-700">Acak Urutan Soal untuk Setiap Kandidat</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_options_randomized" value="1" checked class="w-4 h-4 rounded text-indigo-600">
                    <span class="text-xs font-medium text-slate-700">Acak Urutan Opsi Jawaban (A-D)</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="show_result_to_candidate" value="1" class="w-4 h-4 rounded text-indigo-600">
                    <span class="text-xs font-medium text-slate-700">Tampilkan Skor Langsung ke Kandidat</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-indigo-600">
                    <span class="text-xs font-medium text-slate-700">Status Paket Aktif</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="{{ route('hrd.test-packages.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                Simpan & Aktifkan Paket
            </button>
        </div>
    </form>
</div>
@endsection
