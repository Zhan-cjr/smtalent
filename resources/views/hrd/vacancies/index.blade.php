@extends('layouts.hrd', [
    'title' => 'Kelola Lowongan Rekrutmen',
    'headerTitle' => 'Lowongan Pekerjaan & Batch Rekrutmen',
    'headerSubtitle' => 'Atur periode pembukaan lowongan dan kuota kebutuhan tenaga kerja'
])

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false, isEdit: false, formAction: '', formData: { id: null, position_id: '', title: '', quota: 1, start_date: '', end_date: '', status: 'open', description: '' } }">
    <!-- Top Action -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Batch Lowongan Aktif ({{ $vacancies->count() }})</h3>
            <p class="text-xs text-slate-500">Kandidat mendaftar secara online melalui salah satu lowongan yang dibuka</p>
        </div>
        <button @click="isEdit = false; formAction = '{{ route('hrd.vacancies.store') }}'; formData = { id: null, position_id: '', title: '', quota: 2, start_date: '{{ date('Y-m-d') }}', end_date: '{{ date('Y-m-d', strtotime('+30 days')) }}', status: 'open', description: '' }; modalOpen = true;"
                class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
            <span>➕</span> Buka Lowongan Baru
        </button>
    </div>

    <!-- Vacancies List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3 px-6">Judul Lowongan</th>
                        <th class="py-3 px-6">Posisi Jabatan</th>
                        <th class="py-3 px-6 text-center">Kuota Penerimaan</th>
                        <th class="py-3 px-6 text-center">Jumlah Pelamar</th>
                        <th class="py-3 px-6">Periode Pendaftaran</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($vacancies as $vac)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $vac->title }}
                            </td>
                            <td class="py-4 px-6 font-medium text-indigo-600 text-xs">
                                <span class="bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">{{ $vac->position->name }}</span>
                            </td>
                            <td class="py-4 px-6 text-center font-bold text-slate-900">
                                {{ $vac->quota }} Orang
                            </td>
                            <td class="py-4 px-6 text-center">
                                <a href="{{ route('hrd.candidates.index', ['vacancy_id' => $vac->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                    {{ $vac->candidates_count }} Pelamar
                                </a>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $vac->start_date->format('d M Y') }} s/d {{ $vac->end_date->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold 
                                    @if($vac->status === 'open') bg-emerald-100 text-emerald-800
                                    @elseif($vac->status === 'draft') bg-amber-100 text-amber-800
                                    @else bg-slate-100 text-slate-600 @endif">
                                    {{ strtoupper($vac->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <button @click="isEdit = true; formAction = '{{ route('hrd.vacancies.update', $vac->id) }}'; formData = { id: {{ $vac->id }}, position_id: '{{ $vac->position_id }}', title: '{{ addslashes($vac->title) }}', quota: {{ $vac->quota }}, start_date: '{{ $vac->start_date->format('Y-m-d') }}', end_date: '{{ $vac->end_date->format('Y-m-d') }}', status: '{{ $vac->status }}', description: '{{ addslashes($vac->description ?? '') }}' }; modalOpen = true;"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg text-indigo-600 hover:bg-indigo-50 border border-indigo-200 transition">
                                    Edit
                                </button>
                                <form action="{{ route('hrd.vacancies.destroy', $vac->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan ini?');">
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
                            <td colspan="7" class="py-8 text-center text-xs text-slate-400">Belum ada data lowongan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="modalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-900" x-text="isEdit ? 'Edit Lowongan Kerja' : 'Buka Lowongan Kerja Baru'"></h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Posisi Jabatan</label>
                    <select name="position_id" x-model="formData.position_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                        <option value="">-- Pilih Posisi --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Judul Lowongan</label>
                    <input type="text" name="title" x-model="formData.title" required placeholder="Contoh: Rekrutmen Barista Batch 1"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kuota Diterima</label>
                        <input type="number" name="quota" x-model="formData.quota" min="1" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Status Publikasi</label>
                        <select name="status" x-model="formData.status" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                            <option value="open">Buka (Open)</option>
                            <option value="draft">Draft</option>
                            <option value="closed">Tutup (Closed)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Mulai Tanggal</label>
                        <input type="date" name="start_date" x-model="formData.start_date" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Selesai Tanggal</label>
                        <input type="date" name="end_date" x-model="formData.end_date" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Deskripsi / Kualifikasi</label>
                    <textarea name="description" x-model="formData.description" rows="3" placeholder="Informasi syarat pelamar..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition">
                        Simpan Lowongan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
