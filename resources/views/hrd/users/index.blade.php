@extends('layouts.hrd', [
    'title' => 'Kelola Tim HRD & Interviewer',
    'headerTitle' => 'Kelola Tim HRD & Interviewer',
    'headerSubtitle' => 'Manajemen akun staf HRD dan interviewer penilai seleksi SM Talent'
])

@section('content')
<div class="space-y-6" x-data="{
    modalOpen: false,
    isEdit: false,
    formAction: '',
    currentUserId: {{ auth()->id() }},
    formData: {
        id: null,
        name: '',
        email: '',
        phone: '',
        password: '',
        is_active: true
    },
    openCreate() {
        this.isEdit = false;
        this.formAction = '{{ route('hrd.users.store') }}';
        this.formData = {
            id: null,
            name: '',
            email: '',
            phone: '',
            password: '',
            is_active: true
        };
        this.modalOpen = true;
    },
    openEdit(user) {
        this.isEdit = true;
        this.formAction = '/hrd/users/' + user.id;
        this.formData = {
            id: user.id,
            name: user.name,
            email: user.email,
            phone: user.phone || '',
            password: '',
            is_active: Boolean(user.is_active)
        };
        this.modalOpen = true;
    }
}">
    <!-- Info Banner: Publik Access for Candidates -->
    <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-2xl p-4 flex items-start gap-3 text-indigo-900 shadow-xs">
        <span class="text-xl shrink-0">💡</span>
        <div class="text-xs leading-relaxed">
            <strong class="font-bold">Informasi Akses Kandidat:</strong> Pelamar / kandidat dapat langsung memilih lowongan dan mengerjakan tes psikotes secara publik tanpa perlu mendaftar akun atau login. Halaman ini khusus untuk mengelola akun <strong>Tim HRD & Interviewer</strong> yang bertugas mengatur jadwal, bank soal, dan menilai sesi wawancara kandidat.
        </div>
    </div>

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-xl shrink-0">
                👥
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Tim HRD</span>
                <span class="text-xl sm:text-2xl font-black text-slate-900">{{ number_format($stats['total_hrd']) }}</span>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-xl shrink-0">
                🟢
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Akun HRD Aktif</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-600">{{ number_format($stats['active_hrd']) }}</span>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-xl shrink-0">
                ⚪
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Nonaktif</span>
                <span class="text-xl sm:text-2xl font-black text-slate-500">{{ number_format($stats['inactive_hrd']) }}</span>
            </div>
        </div>
    </div>

    <!-- Filters & Action Topbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar Akun Tim HRD / Penilai ({{ $users->total() }})</h3>
                <p class="text-xs text-slate-500">Akun yang berhak masuk ke panel admin SM Talent dan menilai sesi interview</p>
            </div>
            <button @click="openCreate()"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
                <span>➕</span> Tambah Anggota HRD Baru
            </button>
        </div>

        <form action="{{ route('hrd.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-100">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status Akun</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="1" {{ $statusFilter === '1' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                    <option value="0" {{ $statusFilter === '0' ? 'selected' : '' }}>Nonaktif (Tidak Dapat Login)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Cari Nama / Email / HP</label>
                <div class="flex items-center gap-1.5">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau email HRD..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500">
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">Cari</button>
                    @if($statusFilter !== '' || $search)
                        <a href="{{ route('hrd.users.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold" title="Reset Filter">✕</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table of Users -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- Desktop Table View -->
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3 px-6">Anggota HRD</th>
                        <th class="py-3 px-6">Kontak / HP</th>
                        <th class="py-3 px-6 text-center">Interview Dinilai</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6">Terdaftar Sejak</th>
                        <th class="py-3 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl font-bold text-xs flex items-center justify-center shrink-0 bg-violet-100 text-violet-700">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="text-[10px] bg-indigo-100 text-indigo-700 font-bold px-1.5 py-0.2 rounded-md">(Anda)</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-slate-400">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs">
                                <span class="font-medium text-slate-700">{{ $user->phone ?: '-' }}</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-violet-50 text-violet-700 border border-violet-100">
                                    🎙️ {{ $user->conducted_interviews_count }} Kandidat
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                                <button @click="openEdit({{ Js::from($user) }})"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg text-indigo-600 hover:bg-indigo-50 border border-indigo-200 transition">
                                    Edit
                                </button>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('hrd.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun HRD {{ $user->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-rose-600 hover:bg-rose-50 border border-rose-200 transition">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                <div class="text-3xl mb-2">👥</div>
                                Tidak ada data akun tim HRD yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($users as $user)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl font-bold text-xs flex items-center justify-center shrink-0 bg-violet-100 text-violet-700">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                    <span>{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                        <span class="text-[10px] bg-indigo-100 text-indigo-700 font-bold px-1.5 rounded-md">(Anda)</span>
                                    @endif
                                </h4>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div>
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                    Off
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100 space-y-1 text-slate-600">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Interview Dinilai:</span>
                            <span class="font-bold text-violet-700">{{ $user->conducted_interviews_count }} Kandidat</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">No. HP / WA:</span>
                            <span class="font-semibold text-slate-800">{{ $user->phone ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Terdaftar:</span>
                            <span class="text-slate-600">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button @click="openEdit({{ Js::from($user) }})"
                                class="flex-1 py-2 text-center text-xs font-bold rounded-xl text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition">
                            Edit Profil
                        </button>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('hrd.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun HRD {{ $user->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3.5 py-2 text-xs font-bold rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-xs text-slate-400 p-4">
                    <div class="text-3xl mb-2">👥</div>
                    Tidak ada data tim HRD yang ditemukan.
                </div>
            @endforelse
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Create / Edit HRD User) -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="modalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-bold text-slate-900" x-text="isEdit ? 'Edit Akun Tim HRD' : 'Tambah Anggota HRD Baru'"></h3>
                    <p class="text-xs text-slate-500">Beri hak akses login dan penilai interview</p>
                </div>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-2xl leading-none">&times;</button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" x-model="formData.name" required placeholder="Contoh: HRD Sdr. Budi Santoso"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-sm font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email Login HRD</label>
                        <input type="email" name="email" x-model="formData.email" required placeholder="hrd.nama@perusahaan.com"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">No. WhatsApp / HP</label>
                        <input type="text" name="phone" x-model="formData.phone" placeholder="08123456789"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-sm font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                        <span x-text="isEdit ? 'Password Baru (Opsional)' : 'Password Login'"></span>
                    </label>
                    <input type="password" name="password" x-model="formData.password"
                           :required="!isEdit"
                           placeholder="Minimal 6 karakter"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-sm font-medium">
                    <template x-if="isEdit">
                        <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika tidak ingin mengganti password</p>
                    </template>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="user_is_active" value="1" :checked="formData.is_active"
                           :disabled="isEdit && formData.id === currentUserId"
                           class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500 disabled:cursor-not-allowed">
                    <label for="user_is_active" class="text-xs font-bold text-slate-700">Akun HRD Aktif (Bisa Login)</label>
                </div>
                <template x-if="isEdit && formData.id === currentUserId">
                    <p class="text-[10px] text-amber-600 font-semibold">*Anda tidak dapat menonaktifkan akun sendiri</p>
                </template>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition">
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Buat Akun HRD'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
