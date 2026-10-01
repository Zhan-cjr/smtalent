<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian Psikotes - {{ $package->name }} - SM Talent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; user-select: none; }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex flex-col"
      x-data="testScreen({
          questions: {{ Js::from($questionsData) }},
          remainingSeconds: {{ $remainingSeconds }},
          autosaveUrl: '{{ route('public.test.autosave', $token) }}',
          submitUrl: '{{ route('public.test.submit', $token) }}',
          csrfToken: '{{ csrf_token() }}'
      })"
      x-init="initTimer()">

    <!-- Fixed Header with Timer (Mobile-friendly) -->
    <header class="bg-slate-900 border-b border-slate-800 px-3 sm:px-6 lg:px-8 py-2.5 sm:py-0 sm:h-20 flex items-center justify-between sticky top-0 z-40 shadow-xl gap-2">
        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold text-base sm:text-lg shadow-md shadow-indigo-500/20 shrink-0">
                ⚡
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="font-extrabold text-white text-xs sm:text-sm tracking-tight leading-none truncate">
                        {{ $package->name }}
                    </span>
                    <a href="https://www.instagram.com/amn4ll?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw%3D%3D" 
                       target="_blank" rel="noopener noreferrer" 
                       class="text-[9px] sm:text-[10px] text-indigo-400 hover:text-indigo-300 font-bold hover:underline transition shrink-0 hidden xs:inline">
                        by ZhanSoft
                    </a>
                </div>
                <p class="text-[11px] text-slate-400 truncate">
                    <strong class="text-white">{{ $candidate->name }}</strong> &bull; {{ $candidate->vacancy?->position?->name }}
                </p>
            </div>
        </div>

        <!-- Timer & Actions -->
        <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
            <!-- Mobile Toggle Question Grid -->
            <button @click="showMobilePalette = true" type="button"
                    class="lg:hidden px-2.5 py-1.5 rounded-xl bg-slate-800 border border-slate-700 text-slate-200 text-xs font-bold flex items-center gap-1">
                <span>🔢</span>
                <span x-text="(currentIndex + 1) + '/' + questions.length"></span>
            </button>

            <!-- Timer Badge -->
            <div class="px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl border flex items-center gap-1.5 sm:gap-2 shrink-0"
                 :class="remainingSeconds < 300 ? 'bg-rose-500/20 border-rose-500/40 text-rose-400 animate-pulse' : 'bg-slate-800 border-slate-700 text-white'">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 hidden xs:inline">Sisa:</span>
                <span class="font-mono text-sm sm:text-xl font-black" x-text="formattedTimer">--:--</span>
            </div>

            <button @click="showConfirmSubmit = true" type="button"
                    class="hidden sm:inline-flex items-center px-4 py-2 text-xs font-extrabold bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl shadow-md shadow-emerald-600/20 transition">
                Selesai / Submit
            </button>
        </div>
    </header>

    <!-- Main Workspace -->
    <div class="flex-1 max-w-7xl w-full mx-auto p-3 sm:p-6 lg:p-8 grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 overflow-y-auto">
        <!-- Question Card (Left 8 Cols) -->
        <div class="lg:col-span-8 flex flex-col justify-between bg-slate-900 border border-slate-800 rounded-3xl p-4 sm:p-7 md:p-8 shadow-2xl space-y-5 sm:space-y-6">
            <template x-if="currentQuestion">
                <div class="space-y-4 sm:space-y-6">
                    <!-- Top Question Meta -->
                    <div class="flex items-center justify-between pb-3 sm:pb-4 border-b border-slate-800 gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 font-extrabold text-xs">
                                Soal No. <span x-text="currentIndex + 1"></span> dari <span x-text="questions.length"></span>
                            </span>
                            <span class="text-xs font-semibold text-slate-400" x-text="currentQuestion.category"></span>
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500" x-text="currentQuestion.aspect ? 'Aspek: ' + currentQuestion.aspect : ''"></span>
                    </div>

                    <!-- Question Text -->
                    <div class="text-sm sm:text-base md:text-lg font-bold text-white leading-relaxed whitespace-pre-line" x-text="currentQuestion.text"></div>

                    <!-- Multiple Choice Options -->
                    <template x-if="currentQuestion.type === 'multiple_choice'">
                        <div class="space-y-2.5 sm:space-y-3 pt-1 sm:pt-2">
                            <template x-for="opt in currentQuestion.options" :key="opt.id">
                                <label @click="selectOption(opt.id)"
                                       class="flex items-center gap-3 sm:gap-4 p-3.5 sm:p-4 rounded-2xl border transition cursor-pointer active:scale-99"
                                       :class="currentQuestion.selected_option_id === opt.id 
                                            ? 'bg-indigo-600/20 border-indigo-500 text-white ring-2 ring-indigo-500/30 font-semibold' 
                                            : 'bg-slate-950/60 border-slate-800 hover:bg-slate-800/60 text-slate-300'">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center font-mono font-extrabold text-xs shrink-0 transition"
                                         :class="currentQuestion.selected_option_id === opt.id ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/40' : 'bg-slate-800 text-slate-400'">
                                        <span x-text="opt.key"></span>
                                    </div>
                                    <span class="text-xs sm:text-sm leading-snug" x-text="opt.text"></span>
                                </label>
                            </template>
                        </div>
                    </template>

                    <!-- Likert Scale 1-5 (Mobile responsive) -->
                    <template x-if="currentQuestion.type === 'likert_scale'">
                        <div class="space-y-3 pt-1 sm:pt-2">
                            <p class="text-xs text-slate-400 font-medium">Pilihlah skala yang paling menggambarkan sikap Anda:</p>
                            <div class="grid grid-cols-5 gap-1.5 sm:gap-2 text-center">
                                <template x-for="val in [1, 2, 3, 4, 5]" :key="val">
                                    <button type="button" @click="selectLikert(val)"
                                            class="py-3 sm:py-4 px-1 sm:px-2 rounded-2xl border transition flex flex-col items-center justify-center gap-1"
                                            :class="currentQuestion.likert_value === val 
                                                ? 'bg-violet-600 border-violet-400 text-white font-black shadow-lg shadow-violet-600/30 scale-102 sm:scale-105' 
                                                : 'bg-slate-950 border-slate-800 hover:bg-slate-800 text-slate-300'">
                                        <span class="text-base sm:text-lg font-black" x-text="val"></span>
                                        <span class="text-[8px] sm:text-[9px] uppercase font-bold text-slate-400 leading-tight line-clamp-1" 
                                              x-text="val === 1 ? 'Sgt Tidak' : (val === 2 ? 'Tidak' : (val === 3 ? 'Netral' : (val === 4 ? 'Sesuai' : 'Sgt Sesuai')))"></span>
                                    </button>
                                </template>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-500 px-1 font-semibold">
                                <span>1 = Sangat Tidak Sesuai</span>
                                <span>5 = Sangat Sesuai</span>
                            </div>
                        </div>
                    </template>

                    <!-- Autosave Indicator Status -->
                    <div class="flex items-center justify-between text-xs text-slate-500 pt-2">
                        <span x-show="isSaving" class="text-amber-400 font-bold flex items-center gap-1.5 text-xs">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span> Menyimpan jawaban...
                        </span>
                        <span x-show="!isSaving && saveStatus" class="text-emerald-400 font-bold flex items-center gap-1.5 text-xs" x-text="saveStatus"></span>
                    </div>
                </div>
            </template>

            <!-- Bottom Navigation Controls -->
            <div class="pt-4 sm:pt-6 border-t border-slate-800 flex items-center justify-between gap-2 sm:gap-4">
                <button type="button" @click="prevQuestion()" :disabled="currentIndex === 0"
                        class="px-3.5 sm:px-5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed text-xs font-bold text-slate-200 transition">
                    &larr; <span class="hidden sm:inline">Sebelumnya</span>
                </button>

                <!-- Mobile Quick Jump / Palette Toggle -->
                <button @click="showMobilePalette = true" type="button"
                        class="lg:hidden px-3 py-2 text-xs font-bold bg-slate-800 border border-slate-700 text-indigo-400 rounded-xl">
                    Nomor: <strong class="text-white" x-text="currentIndex + 1"></strong>
                </button>

                <div class="sm:hidden">
                    <button @click="showConfirmSubmit = true" type="button"
                            class="px-3.5 py-2.5 text-xs font-extrabold bg-emerald-600 text-white rounded-xl">
                        Submit
                    </button>
                </div>

                <button type="button" @click="nextQuestion()" :disabled="currentIndex === questions.length - 1"
                        class="px-3.5 sm:px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 disabled:opacity-30 disabled:cursor-not-allowed text-xs font-bold text-white shadow-md shadow-indigo-600/20 transition">
                    <span class="hidden sm:inline">Berikutnya</span> &rarr;
                </button>
            </div>
        </div>

        <!-- Question Grid Navigator (Right 4 Cols on Desktop) -->
        <div class="hidden lg:flex lg:col-span-4 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-sm font-bold text-white">Navigasi Nomor Soal</h3>
                    <span class="text-xs font-bold text-indigo-400">
                        <span x-text="answeredCount"></span> / <span x-text="questions.length"></span> Terjawab
                    </span>
                </div>

                <!-- Legend -->
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 pb-2">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-emerald-500"></span> Sudah</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-slate-800 border border-slate-700"></span> Belum</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-indigo-600 ring-2 ring-indigo-400"></span> Aktif</span>
                </div>

                <!-- Numbers Matrix Grid -->
                <div class="grid grid-cols-5 gap-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                    <template x-for="(q, idx) in questions" :key="q.question_id">
                        <button type="button" @click="jumpTo(idx)"
                                class="h-10 rounded-xl font-mono text-xs font-extrabold flex items-center justify-center transition"
                                :class="idx === currentIndex 
                                    ? 'bg-indigo-600 text-white ring-2 ring-indigo-400 shadow-md shadow-indigo-600/40' 
                                    : (q.is_answered ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-800 text-slate-400 hover:bg-slate-700')">
                            <span x-text="idx + 1"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Submit Final Button in Sidebar -->
            <button type="button" @click="showConfirmSubmit = true"
                    class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-xs shadow-xl shadow-emerald-600/30 transition">
                Selesai & Submit Ujian ✅
            </button>
        </div>
    </div>

    <!-- Mobile Slide-up Question Palette Drawer -->
    <div x-show="showMobilePalette" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/80 backdrop-blur-md lg:hidden" style="display: none;">
        <div @click.away="showMobilePalette = false" class="bg-slate-900 border border-slate-800 rounded-t-3xl sm:rounded-3xl p-5 max-w-md w-full shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-white">Navigasi Nomor Soal</h3>
                    <p class="text-[11px] text-slate-400">
                        <strong class="text-indigo-400" x-text="answeredCount"></strong> dari <strong class="text-white" x-text="questions.length"></strong> soal terjawab
                    </p>
                </div>
                <button @click="showMobilePalette = false" class="text-slate-400 hover:text-white text-2xl font-bold p-1 leading-none">&times;</button>
            </div>

            <!-- Legend -->
            <div class="flex items-center justify-around text-[10px] font-bold text-slate-400 py-1 bg-slate-950/60 rounded-xl">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-500"></span> Sudah</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-slate-800 border border-slate-700"></span> Belum</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-indigo-600 ring-2 ring-indigo-400"></span> Aktif</span>
            </div>

            <!-- Matrix -->
            <div class="grid grid-cols-5 gap-2 max-h-60 overflow-y-auto pr-1">
                <template x-for="(q, idx) in questions" :key="q.question_id">
                    <button type="button" @click="jumpTo(idx); showMobilePalette = false"
                            class="h-10 rounded-xl font-mono text-xs font-extrabold flex items-center justify-center transition"
                            :class="idx === currentIndex 
                                ? 'bg-indigo-600 text-white ring-2 ring-indigo-400 shadow-md shadow-indigo-600/40' 
                                : (q.is_answered ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-800 text-slate-400')">
                        <span x-text="idx + 1"></span>
                    </button>
                </template>
            </div>

            <button type="button" @click="showMobilePalette = false; showConfirmSubmit = true"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold text-xs">
                Selesai & Submit Ujian ✅
            </button>
        </div>
    </div>

    <!-- Confirm Submit Modal -->
    <div x-show="showConfirmSubmit" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" style="display: none;">
        <div @click.away="showConfirmSubmit = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl text-center space-y-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-3xl mx-auto font-bold">
                🎯
            </div>
            <h3 class="text-lg sm:text-xl font-extrabold text-white">Konfirmasi Submit Ujian</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                Anda telah menjawab <strong class="text-white" x-text="answeredCount"></strong> dari <strong class="text-white" x-text="questions.length"></strong> soal. 
                Apakah Anda yakin ingin menyelesaikan ujian psikotes sekarang?
            </p>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-800">
                <button type="button" @click="showConfirmSubmit = false" class="flex-1 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">
                    Periksa Kembali
                </button>
                <button type="button" @click="submitTest()" :disabled="isSubmitting" class="flex-1 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30">
                    <span x-show="!isSubmitting">Ya, Kirim Jawaban</span>
                    <span x-show="isSubmitting">Mengirim...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Test Screen Controller Logic -->
    <script>
        function testScreen(config) {
            return {
                questions: config.questions,
                currentIndex: 0,
                remainingSeconds: config.remainingSeconds,
                autosaveUrl: config.autosaveUrl,
                submitUrl: config.submitUrl,
                csrfToken: config.csrfToken,
                isSaving: false,
                isSubmitting: false,
                saveStatus: '',
                showConfirmSubmit: false,
                showMobilePalette: false,
                timerInterval: null,

                get currentQuestion() {
                    return this.questions[this.currentIndex];
                },

                get answeredCount() {
                    return this.questions.filter(q => q.is_answered).length;
                },

                get formattedTimer() {
                    let mins = Math.floor(this.remainingSeconds / 60);
                    let secs = this.remainingSeconds % 60;
                    return (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
                },

                initTimer() {
                    this.timerInterval = setInterval(() => {
                        if (this.remainingSeconds > 0) {
                            this.remainingSeconds--;
                        } else {
                            clearInterval(this.timerInterval);
                            alert('Waktu ujian telah habis! Sistem secara otomatis akan menyimpan & mensubmit seluruh jawaban Anda.');
                            this.submitTest();
                        }
                    }, 1000);
                },

                nextQuestion() {
                    if (this.currentIndex < this.questions.length - 1) {
                        this.currentIndex++;
                    }
                },

                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    }
                },

                jumpTo(idx) {
                    this.currentIndex = idx;
                },

                async selectOption(optionId) {
                    this.currentQuestion.selected_option_id = optionId;
                    this.currentQuestion.is_answered = true;
                    await this.persistAnswer(this.currentQuestion.question_id, optionId, null);
                },

                async selectLikert(val) {
                    this.currentQuestion.likert_value = val;
                    this.currentQuestion.is_answered = true;
                    await this.persistAnswer(this.currentQuestion.question_id, null, val);
                },

                async persistAnswer(questionId, optionId, likertVal) {
                    this.isSaving = true;
                    this.saveStatus = '';
                    try {
                        let res = await fetch(this.autosaveUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                question_id: questionId,
                                option_id: optionId,
                                likert_value: likertVal
                            })
                        });
                        let data = await res.json();
                        if (data.success) {
                            this.saveStatus = '✓ Tersimpan';
                        }
                    } catch (err) {
                        console.error('Autosave error:', err);
                    } finally {
                        this.isSaving = false;
                    }
                },

                async submitTest() {
                    this.isSubmitting = true;
                    try {
                        let res = await fetch(this.submitUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        let data = await res.json();
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        }
                    } catch (e) {
                        window.location.href = '{{ route('home') }}';
                    }
                }
            };
        }
    </script>
</body>
</html>
