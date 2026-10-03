@extends('layouts.hrd', [
    'title' => 'Laporan & Export Data',
    'headerTitle' => 'Laporan & Export Rekapitulasi',
    'headerSubtitle' => 'Unduh laporan rekapitulasi nilai psikotes dan keputusan seleksi dalam format Excel'
])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Rekap Nilai Psikotes -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between space-y-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                    <span>🎯</span> Rekap Khusus Psikotes
                </div>
                <h3 class="text-lg font-bold text-slate-900">Rekap Nilai Psikotes</h3>
                <p class="text-xs text-slate-500">Ekspor daftar leaderboard, durasi pengerjaan, skor psikotes, dan status kelulusan passing grade ke file Excel (.xlsx).</p>
            </div>

            <form action="{{ route('hrd.reports.export.psychotest') }}" method="GET" class="space-y-4 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Lowongan Batch</label>
                    <select name="vacancy_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-xs font-medium">
                        <option value="">Semua Lowongan</option>
                        @foreach($vacancies as $vac)
                            <option value="{{ $vac->id }}">{{ $vac->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Posisi Jabatan</label>
                    <select name="position_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-xs font-medium">
                        <option value="">Semua Posisi</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Status Kelulusan</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-xs font-medium">
                        <option value="">Semua Status</option>
                        <option value="LULUS_PSIKOTES">Lulus Psikotes</option>
                        <option value="TIDAK_LULUS">Tidak Lulus</option>
                        <option value="INTERVIEW">Lanjut Interview</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-2">
                        <span>📊</span> Unduh Rekap Psikotes (.xlsx)
                    </button>
                </div>
            </form>
        </div>

        <!-- Card 2: Rekap Hasil Akhir Seleksi -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between space-y-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                    <span>🏆</span> Rekap Seleksi Akhir
                </div>
                <h3 class="text-lg font-bold text-slate-900">Rekap Hasil Seleksi Akhir</h3>
                <p class="text-xs text-slate-500">Ekspor rekapitulasi gabungan nilai psikotes (60%) + interview (40%), nilai akhir, serta status keputusan rekrutmen ke file Excel.</p>
            </div>

            <form action="{{ route('hrd.reports.export.excel') }}" method="GET" class="space-y-4 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Lowongan Batch</label>
                    <select name="vacancy_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-xs font-medium">
                        <option value="">Semua Lowongan</option>
                        @foreach($vacancies as $vac)
                            <option value="{{ $vac->id }}">{{ $vac->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Posisi Jabatan</label>
                    <select name="position_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-xs font-medium">
                        <option value="">Semua Posisi</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Keputusan Final</label>
                    <select name="final_status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-xs font-medium">
                        <option value="">Semua Keputusan</option>
                        <option value="DITERIMA">Diterima</option>
                        <option value="DIPERTIMBANGKAN">Dipertimbangkan</option>
                        <option value="TIDAK_DITERIMA">Tidak Diterima</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                        <span>📊</span> Unduh Rekap Seleksi (.xlsx)
                    </button>
                </div>
            </form>
        </div>
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
