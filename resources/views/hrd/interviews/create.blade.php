@extends('layouts.hrd', [
    'title' => 'Jadwalkan Wawancara',
    'headerTitle' => 'Jadwalkan Sesi Wawancara',
    'headerSubtitle' => 'Pilih kandidat yang telah lolos psikotes untuk tahapan interview'
])

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
    <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Formulir Penjadwalan Interview</h3>
            <p class="text-xs text-slate-500">Tentukan tanggal, jam, ruangan, dan penilai</p>
        </div>
        <a href="{{ route('hrd.interviews.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('hrd.interviews.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pilih Kandidat</label>
            <select name="candidate_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                <option value="">-- Pilih Kandidat Pelamar --</option>
                @foreach($eligibleCandidates as $cand)
                    @php
                        $lockedByOther = $cand->activeInterview && $cand->activeInterview->interviewer_id && $cand->activeInterview->interviewer_id !== auth()->id();
                    @endphp
                    <option value="{{ $cand->id }}" {{ (isset($candidate) && $candidate->id == $cand->id) ? 'selected' : '' }} {{ $lockedByOther ? 'disabled class=text-slate-400' : '' }}>
                        {{ $cand->name }} - {{ $cand->vacancy?->position?->name }} (Skor Psikotes: {{ $cand->final_psychotest_score ?? 'N/A' }}) {{ $lockedByOther ? '[🔒 Terkunci: ' . ($cand->activeInterview->interviewer?->name ?? 'HRD Lain') . ']' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Interviewer / Penilai HRD</label>
                <select name="interviewer_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    @foreach($interviewers as $itwer)
                        <option value="{{ $itwer->id }}" {{ auth()->id() == $itwer->id ? 'selected' : '' }}>{{ $itwer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Waktu & Jam Pelaksanaan</label>
                <input type="datetime-local" name="scheduled_at" value="{{ date('Y-m-d\TH:i', strtotime('+1 day 09:00')) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tempat / Ruang Wawancara / Tautan Online</label>
            <input type="text" name="location" value="Ruang Interview 1 (Head Office)" required placeholder="Contoh: Ruang Rapat 2 / Google Meet..."
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Catatan Instruksi untuk Kandidat</label>
            <textarea name="notes" rows="3" placeholder="Bawa CV fisik, berpakaian kemeja rapi, dll..."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="{{ route('hrd.interviews.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm">
                Simpan & Jadwalkan Wawancara
            </button>
        </div>
    </form>
</div>
@endsection
