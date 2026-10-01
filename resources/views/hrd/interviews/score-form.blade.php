@extends('layouts.hrd', [
    'title' => 'Input Nilai Wawancara',
    'headerTitle' => 'Form Penilaian Wawancara (8 Aspek)',
    'headerSubtitle' => 'Evaluasi kompetensi kandidat secara terukur pada skala 0 - 100'
])

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    scores: {
        'Komunikasi': {{ $interview->scores->firstWhere('aspect_name', 'Komunikasi')?->score ?? 80 }},
        'Sikap': {{ $interview->scores->firstWhere('aspect_name', 'Sikap')?->score ?? 80 }},
        'Motivasi': {{ $interview->scores->firstWhere('aspect_name', 'Motivasi')?->score ?? 80 }},
        'Pengalaman': {{ $interview->scores->firstWhere('aspect_name', 'Pengalaman')?->score ?? 80 }},
        'Pengetahuan': {{ $interview->scores->firstWhere('aspect_name', 'Pengetahuan')?->score ?? 80 }},
        'Problem Solving': {{ $interview->scores->firstWhere('aspect_name', 'Problem Solving')?->score ?? 80 }},
        'Kerja Sama': {{ $interview->scores->firstWhere('aspect_name', 'Kerja Sama')?->score ?? 80 }},
        'Kedisiplinan': {{ $interview->scores->firstWhere('aspect_name', 'Kedisiplinan')?->score ?? 80 }}
    },
    get average() {
        let values = Object.values(this.scores).map(v => parseFloat(v) || 0);
        let sum = values.reduce((a, b) => a + b, 0);
        return (sum / values.length).toFixed(2);
    }
}">
    <!-- Candidate Overview Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-violet-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-violet-600/20">
                🎙️
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">{{ $interview->candidate->name }}</h3>
                <p class="text-xs text-slate-500">Posisi: <strong class="text-slate-800">{{ $interview->candidate->vacancy?->position?->name }}</strong> | Skor Psikotes: <strong class="text-indigo-600">{{ $interview->candidate->final_psychotest_score ?? '-' }}</strong></p>
            </div>
        </div>

        <div class="text-right">
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Rata-rata Skor Interview</span>
            <span class="text-3xl font-black text-violet-600" x-text="average">0.00</span>
        </div>
    </div>

    <!-- Scoring Form -->
    <form action="{{ route('hrd.interviews.score.submit', $interview->id) }}" method="POST" class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-8">
        @csrf

        <!-- 8 Components Grid -->
        <div class="space-y-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 pb-2 border-b border-slate-100">1. Penilaian 8 Aspek Utama (Skala 0 - 100)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($aspectNames as $aspect)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-extrabold text-slate-800">{{ $aspect }}</label>
                            <span class="font-mono font-black text-sm text-violet-700" x-text="scores['{{ $aspect }}']">80</span>
                        </div>
                        <input type="range" min="0" max="100" step="1" x-model="scores['{{ $aspect }}']" class="w-full h-3 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-violet-600">
                        <input type="number" min="0" max="100" step="0.5" name="aspects[{{ $aspect }}]" x-model="scores['{{ $aspect }}']" required
                               class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-base sm:text-xs font-bold text-right focus:border-violet-500">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Qualitative Notes -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">2. Evaluasi Kualitatif & Rekomendasi</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kelebihan / Kekuatan Kandidat</label>
                    <textarea name="strengths" rows="3" placeholder="Sikap ramah, pengalaman relevan, inisiatif tinggi..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-sm font-medium">{{ old('strengths', $interview->strengths) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Hal yang Perlu Ditingkatkan</label>
                    <textarea name="weaknesses" rows="3" placeholder="Pemahaman teknis POS masih dasar, perlu adaptasi shift..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-sm font-medium">{{ old('weaknesses', $interview->weaknesses) }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Rekomendasi Akhir Interviewer</label>
                <select name="recommendation" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-sm font-bold">
                    <option value="DISARANKAN" {{ $interview->recommendation === 'DISARANKAN' ? 'selected' : '' }}>🟢 DISARANKAN (Sangat Cocok untuk Diterima)</option>
                    <option value="DIPERTIMBANGKAN" {{ $interview->recommendation === 'DIPERTIMBANGKAN' ? 'selected' : '' }}>🟡 DIPERTIMBANGKAN (Sebagai Kandidat Cadangan)</option>
                    <option value="TIDAK_DISARANKAN" {{ $interview->recommendation === 'TIDAK_DISARANKAN' ? 'selected' : '' }}>🔴 TIDAK DISARANKAN (Tidak Memenuhi Standar)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Catatan Tambahan Interview</label>
                <textarea name="notes" rows="2" placeholder="Catatan tambahan hasil tanya jawab..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 text-base sm:text-sm font-medium">{{ old('notes', $interview->notes) }}</textarea>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="{{ route('hrd.interviews.index') }}" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs text-center hover:bg-slate-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs shadow-md shadow-violet-600/20 text-center">
                Simpan Penilaian & Kalkulasi Nilai Akhir
            </button>
        </div>
    </form>
</div>
@endsection
