@extends('layouts.hrd', [
    'title' => 'Laporan & Export Data',
    'headerTitle' => 'Laporan & Export Rekapitulasi',
    'headerSubtitle' => 'Unduh laporan rekapitulasi nilai psikotes dan keputusan seleksi dalam format Excel'
])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Export Rekap Hasil Seleksi ke Excel</h3>
            <p class="text-xs text-slate-500">Pilih kriteria filter data yang ingin diekspor ke format spreadsheet (.xlsx)</p>
        </div>

        <form action="{{ route('hrd.reports.export.excel') }}" method="GET" class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Berdasarkan Lowongan Batch</label>
                <select name="vacancy_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    <option value="">Semua Lowongan</option>
                    @foreach($vacancies as $vac)
                        <option value="{{ $vac->id }}">{{ $vac->title }} ({{ $vac->position->name }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Berdasarkan Posisi Jabatan</label>
                <select name="position_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-sm font-medium">
                    <option value="">Semua Posisi</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                    <span>📊</span> Unduh File Excel (.xlsx)
                </button>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="p-5 rounded-3xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-900 space-y-2">
        <h4 class="font-bold flex items-center gap-2"><span>ℹ️</span> Informasi Ekspor Dokumen Individual:</h4>
        <p class="leading-relaxed">
            Untuk mengunduh berkas lembar hasil individual per kandidat (PDF), buka menu <strong>Kandidat</strong> atau <strong>Ranking Akhir</strong>, lalu klik tombol <strong>PDF / Cetak Lembar PDF</strong> pada baris kandidat yang bersangkutan.
        </p>
    </div>
</div>
@endsection
