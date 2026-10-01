@extends('layouts.hrd', [
    'title' => 'Kelola Posisi / Jabatan',
    'headerTitle' => 'Master Posisi & Jabatan',
    'headerSubtitle' => 'Kelola daftar posisi pekerjaan dan paket psikotes yang ditautkan'
])

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false, isEdit: false, formAction: '', formData: { id: null, code: '', name: '', description: '', is_active: true } }">
    <!-- Action Topbar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Posisi Aktif ({{ $positions->count() }})</h3>
            <p class="text-xs text-slate-500">Setiap posisi dapat memiliki paket psikotes dan bank soal mandiri</p>
        </div>
        <button @click="isEdit = false; formAction = '{{ route('hrd.positions.store') }}'; formData = { id: null, code: '', name: '', description: '', is_active: true }; modalOpen = true;"
                class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
            <span>➕</span> Tambah Posisi Baru
        </button>
    </div>

    <!-- Table of Positions -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3 px-6">Kode</th>
                        <th class="py-3 px-6">Nama Posisi</th>
                        <th class="py-3 px-6">Deskripsi Pekerjaan</th>
                        <th class="py-3 px-6 text-center">Lowongan Terkait</th>
                        <th class="py-3 px-6 text-center">Soal Khusus</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($positions as $pos)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-mono font-bold text-indigo-600 text-xs">
                                {{ $pos->code }}
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $pos->name }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500 max-w-xs truncate">
                                {{ $pos->description ?: '-' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $pos->vacancies_count }} Lowongan
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                    {{ $pos->questions_count }} Soal
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($pos->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <button @click="isEdit = true; formAction = '{{ route('hrd.positions.update', $pos->id) }}'; formData = { id: {{ $pos->id }}, code: '{{ addslashes($pos->code) }}', name: '{{ addslashes($pos->name) }}', description: '{{ addslashes($pos->description ?? '') }}', is_active: {{ $pos->is_active ? 'true' : 'false' }} }; modalOpen = true;"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg text-indigo-600 hover:bg-indigo-50 border border-indigo-200 transition">
                                    Edit
                                </button>
                                <form action="{{ route('hrd.positions.destroy', $pos->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus posisi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-rose-600 hover:bg-rose-50 border border-rose-200 transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-400">Belum ada data posisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="modalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-900" x-text="isEdit ? 'Edit Posisi Jabatan' : 'Tambah Posisi Jabatan Baru'"></h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kode Posisi (Singkatan)</label>
                    <input type="text" name="code" x-model="formData.code" required placeholder="Contoh: PRM, BAR, KSR"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Posisi / Jabatan</label>
                    <input type="text" name="name" x-model="formData.name" required placeholder="Contoh: Pramuniaga Toko"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Deskripsi Tanggung Jawab</label>
                    <textarea name="description" x-model="formData.description" rows="3" placeholder="Uraikan ruang lingkup pekerjaan..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-medium"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" :checked="formData.is_active"
                           class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                    <label for="is_active" class="text-xs font-bold text-slate-700">Status Posisi Aktif</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition">
                        Simpan Data Posisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
