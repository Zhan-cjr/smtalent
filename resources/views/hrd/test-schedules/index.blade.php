@extends('layouts.hrd', [
    'title' => 'Jadwal Tes Psikotes',
    'headerTitle' => 'Manajemen Jadwal Tes Psikotes',
    'headerSubtitle' => 'Atur periode buka/tutup sesi ujian psikotes. Kandidat hanya bisa mengerjakan saat jadwal dibuka.'
])

@section('content')
<div class="space-y-6" x-data="{
    modalOpen: false,
    isEdit: false,
    formAction: '',
    formData: {
        id: null,
        test_package_id: '',
        name: '',
        start_time: '',
        end_time: '',
        is_active: true
    },
    openCreate() {
        this.isEdit = false;
        this.formAction = '{{ route('hrd.test-schedules.store') }}';
        this.formData = {
            id: null,
            test_package_id: '{{ $packages->first()?->id ?? '' }}',
            name: '',
            start_time: '{{ now()->format('Y-m-d\TH:i') }}',
            end_time: '{{ now()->addHours(3)->format('Y-m-d\TH:i') }}',
            is_active: true
        };
        this.modalOpen = true;
    },
    openEdit(item) {
        this.isEdit = true;
        this.formAction = '/hrd/test-schedules/' + item.id;
        this.formData = {
            id: item.id,
            test_package_id: item.test_package_id,
            name: item.name,
            start_time: item.start_time ? item.start_time.substring(0, 16) : '',
            end_time: item.end_time ? item.end_time.substring(0, 16) : '',
            is_active: Boolean(item.is_active)
        };
        this.modalOpen = true;
    }
}">
    <!-- Action Topbar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Jadwal Ujian Psikotes ({{ $schedules->total() }})</h3>
            <p class="text-xs text-slate-500">Kandidat tidak dapat mengklik atau memulai tes di luar jadwal yang aktif.</p>
        </div>
        <button @click="openCreate()"
                class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
            <span>➕</span> Buat Jadwal Tes Baru
        </button>
    </div>

    <!-- Table of Schedules -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- Desktop Table View -->
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3 px-6">Nama Sesi / Jadwal</th>
                        <th class="py-3 px-6">Paket Psikotes & Posisi</th>
                        <th class="py-3 px-6">Rentang Waktu (Mulai & Selesai)</th>
                        <th class="py-3 px-6 text-center">Status Sesi</th>
                        <th class="py-3 px-6 text-center">Status Jadwal</th>
                        <th class="py-3 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($schedules as $sched)
                        @php
                            $isOngoing = $sched->isOngoing();
                            $isUpcoming = $sched->isUpcoming();
                            $isPast = $sched->isPast();
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-bold text-slate-900">
                                <div>{{ $sched->name }}</div>
                                <span class="text-[11px] text-slate-400">ID: #{{ $sched->id }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-800 text-xs">{{ $sched->testPackage?->name ?? '-' }}</div>
                                <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                    {{ $sched->testPackage?->position?->name ?? 'Semua Posisi' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs">
                                <div class="font-semibold text-slate-900">
                                    Mulai: {{ $sched->start_time->format('d M Y - H:i') }} WIB
                                </div>
                                <div class="text-slate-500">
                                    Sampai: {{ $sched->end_time->format('d M Y - H:i') }} WIB
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if(!$sched->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                        Dinonaktifkan
                                    </span>
                                @elseif($isOngoing)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Sedang Buka (Aktif)
                                    </span>
                                @elseif($isUpcoming)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        ⏳ Akan Datang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        ❌ Telah Berakhir
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($sched->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                                        Off
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                                <button @click="openEdit({{ Js::from($sched) }})"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg text-indigo-600 hover:bg-indigo-50 border border-indigo-200 transition">
                                    Edit
                                </button>
                                <form action="{{ route('hrd.test-schedules.destroy', $sched->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
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
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                <div class="text-3xl mb-2">📅</div>
                                Belum ada jadwal ujian psikotes yang dibuat. <br>
                                <span class="text-slate-500 font-medium">Kandidat saat ini tidak dapat mengklik atau mengakses ujian sampai jadwal dibuat.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($schedules as $sched)
                @php
                    $isOngoing = $sched->isOngoing();
                    $isUpcoming = $sched->isUpcoming();
                    $isPast = $sched->isPast();
                @endphp
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $sched->name }}</h4>
                            <div class="text-[11px] text-slate-400">ID: #{{ $sched->id }} &bull; {{ $sched->testPackage?->name ?? '-' }}</div>
                        </div>
                        <div>
                            @if(!$sched->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                    Off
                                </span>
                            @elseif($isOngoing)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Buka
                                </span>
                            @elseif($isUpcoming)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                    Akan Datang
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                    Berakhir
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1 text-slate-600">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Posisi:</span>
                            <span class="font-bold text-slate-800">{{ $sched->testPackage?->position?->name ?? 'Semua Posisi' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Mulai:</span>
                            <span class="font-semibold text-slate-800">{{ $sched->start_time->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Selesai:</span>
                            <span class="font-semibold text-slate-800">{{ $sched->end_time->format('d M Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button @click="openEdit({{ Js::from($sched) }})"
                                class="flex-1 py-2 text-center text-xs font-bold rounded-xl text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition">
                            Edit Jadwal
                        </button>
                        <form action="{{ route('hrd.test-schedules.destroy', $sched->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3.5 py-2 text-xs font-bold rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-xs text-slate-400 p-4">
                    <div class="text-3xl mb-2">📅</div>
                    Belum ada jadwal ujian psikotes yang dibuat.
                </div>
            @endforelse
        </div>

        @if($schedules->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="modalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-bold text-slate-900" x-text="isEdit ? 'Edit Jadwal Psikotes' : 'Buat Jadwal Psikotes Baru'"></h3>
                    <p class="text-xs text-slate-500">Tentukan paket dan rentang waktu ujian dibuka</p>
                </div>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pilih Paket Psikotes</label>
                    <select name="test_package_id" x-model="formData.test_package_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                        <option value="">-- Pilih Paket Ujian --</option>
                        @foreach($packages as $pkg)
                            <option value="{{ $pkg->id }}">
                                {{ $pkg->name }} ({{ $pkg->position?->name ?? 'Umum' }}) - {{ $pkg->duration_minutes }} Menit
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Sesi / Gelombang</label>
                    <input type="text" name="name" x-model="formData.name" required placeholder="Contoh: Sesi Pagi Gelombang 1"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Waktu Mulai Buka</label>
                        <input type="datetime-local" name="start_time" x-model="formData.start_time" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Waktu Selesai Tutup</label>
                        <input type="datetime-local" name="end_time" x-model="formData.end_time" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="sched_is_active" value="1" :checked="formData.is_active"
                           class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                    <label for="sched_is_active" class="text-xs font-bold text-slate-700">Aktifkan Jadwal Ini</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition">
                        Simpan Jadwal Psikotes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
