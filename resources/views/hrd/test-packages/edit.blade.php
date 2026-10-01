@extends('layouts.hrd', [
    'title' => 'Edit Paket Psikotes',
    'headerTitle' => 'Edit Paket Psikotes',
    'headerSubtitle' => 'Perbarui parameter waktu, jumlah soal, dan passing grade'
])

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
    <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Edit Paket: {{ $testPackage->name }}</h3>
            <p class="text-xs text-slate-500">Ubah konfigurasi evaluasi sesuai kebutuhan posisi</p>
        </div>
        <a href="{{ route('hrd.test-packages.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('hrd.test-packages.update', $testPackage->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Paket</label>
                <input type="text" name="name" value="{{ old('name', $testPackage->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Target Posisi Jabatan</label>
                <select name="position_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    <option value="">Semua Posisi (Umum)</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ $testPackage->position_id == $pos->id ? 'selected' : '' }}>{{ $pos->name }} ({{ $pos->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Deskripsi / Petunjuk Tes</label>
            <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">{{ old('description', $testPackage->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Jumlah Soal Ditampilkan</label>
                <input type="number" name="total_questions" value="{{ $testPackage->total_questions }}" min="1" required
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 focus:border-indigo-500 text-sm font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Durasi Waktu (Menit)</label>
                <input type="number" name="duration_minutes" value="{{ $testPackage->duration_minutes }}" min="1" required
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 focus:border-indigo-500 text-sm font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Passing Grade (0 - 100)</label>
                <input type="number" step="0.1" name="passing_grade" value="{{ $testPackage->passing_grade }}" min="0" max="100" required
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-300 focus:border-indigo-500 text-sm font-bold text-emerald-600">
            </div>
        </div>

        <div class="space-y-3 p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Pengaturan Acak & Visibilitas</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_randomized" value="1" {{ $testPackage->is_randomized ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600">
                    <span class="text-xs font-medium text-slate-700">Acak Urutan Soal untuk Setiap Kandidat</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_options_randomized" value="1" {{ $testPackage->is_options_randomized ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600">
                    <span class="text-xs font-medium text-slate-700">Acak Urutan Opsi Jawaban (A-D)</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="show_result_to_candidate" value="1" {{ $testPackage->show_result_to_candidate ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600">
                    <span class="text-xs font-medium text-slate-700">Tampilkan Skor Langsung ke Kandidat</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $testPackage->is_active ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600">
                    <span class="text-xs font-medium text-slate-700">Status Paket Aktif</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="{{ route('hrd.test-packages.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                Perbarui Paket Psikotes
            </button>
        </div>
    </form>
</div>
@endsection
