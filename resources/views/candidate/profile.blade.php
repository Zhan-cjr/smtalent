@extends('layouts.candidate', [
    'title' => 'Profil Pelamar'
])

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
    <div class="pb-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Data Profil Kandidat</h3>
            <p class="text-xs text-slate-500">Pastikan nomor telepon/WhatsApp dan identitas selalu aktif untuk koordinasi HRD</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
            {{ $candidate->vacancy?->position?->name }}
        </span>
    </div>

    <form action="{{ route('candidate.profile.update') }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Alamat Email (Akun)</label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 text-sm font-medium cursor-not-allowed">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">No. Telepon / WhatsApp Aktif</label>
                <input type="text" name="phone" value="{{ old('phone', $candidate->phone) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nomor Induk Kependudukan (NIK)</label>
                <input type="text" name="nik" value="{{ old('nik', $candidate->nik) }}" placeholder="16 digit NIK..."
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Jenis Kelamin</label>
                <select name="gender" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    <option value="L" {{ $candidate->gender === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $candidate->gender === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pendidikan Terakhir</label>
                <input type="text" name="education" value="{{ old('education', $candidate->education) }}" placeholder="SMA / SMK / D3 / S1..."
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Alamat Tempat Tinggal Saat Ini</label>
            <textarea name="address" rows="3" placeholder="Alamat domisili lengkap..."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">{{ old('address', $candidate->address) }}</textarea>
        </div>

        <div class="pt-4 flex items-center justify-end border-t border-slate-100">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition">
                Simpan Perubahan Profil
            </button>
        </div>
    </form>
</div>
@endsection
