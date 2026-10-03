<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Hrd\AuditLogController;
use App\Http\Controllers\Hrd\CandidateManagementController;
use App\Http\Controllers\Hrd\DashboardController;
use App\Http\Controllers\Hrd\InterviewController;
use App\Http\Controllers\Hrd\PositionController;
use App\Http\Controllers\Hrd\QuestionBankController;
use App\Http\Controllers\Hrd\RankingController;
use App\Http\Controllers\Hrd\ReportController;
use App\Http\Controllers\Hrd\SettingController;
use App\Http\Controllers\Hrd\TestPackageController;
use App\Http\Controllers\Hrd\TestScheduleController;
use App\Http\Controllers\Hrd\UserController;
use App\Http\Controllers\Hrd\VacancyController;
use App\Http\Controllers\PublicTestController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// PUBLIC CANDIDATE ROUTES (Tanpa Registrasi / Tanpa Password)
// =========================================================================
Route::get('/', [PublicTestController::class, 'index'])->name('home');
Route::post('/test/start', [PublicTestController::class, 'start'])->name('public.test.start');
Route::get('/test/{token}/screen', [PublicTestController::class, 'showScreen'])->name('public.test.screen');
Route::post('/test/{token}/autosave', [PublicTestController::class, 'autosave'])->name('public.test.autosave');
Route::post('/test/{token}/submit', [PublicTestController::class, 'submitTest'])->name('public.test.submit');
Route::get('/test/{token}/result', [PublicTestController::class, 'showResult'])->name('public.test.result');
Route::get('/check-status', [PublicTestController::class, 'checkStatus'])->name('public.check-status');

// =========================================================================
// HRD / ADMIN AUTHENTICATION
// =========================================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =========================================================================
// HRD / ADMIN PROTECTED PANEL
// =========================================================================
Route::middleware(['auth', 'role.hrd'])->prefix('hrd')->name('hrd.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Posisi
    Route::resource('positions', PositionController::class)->except(['create', 'show', 'edit']);

    // Master Lowongan
    Route::resource('vacancies', VacancyController::class)->except(['create', 'show', 'edit']);

    // Bank Soal
    Route::get('/questions/export-excel', [QuestionBankController::class, 'exportExcel'])->name('questions.export');
    Route::post('/questions/{question}/duplicate', [QuestionBankController::class, 'duplicate'])->name('questions.duplicate');
    Route::resource('questions', QuestionBankController::class);

    // Paket & Jadwal Psikotes
    Route::resource('test-packages', TestPackageController::class);
    Route::resource('test-schedules', TestScheduleController::class)->except(['create', 'show', 'edit']);

    // Manajemen Kandidat
    Route::get('/candidates', [CandidateManagementController::class, 'index'])->name('candidates.index');
    Route::get('/candidates/{candidate}', [CandidateManagementController::class, 'show'])->name('candidates.show');
    Route::post('/candidates/{candidate}/status', [CandidateManagementController::class, 'updateStatus'])->name('candidates.status');

    // Interview & Penilaian 8 Aspek (Single-HRD Claim & Concurrency)
    Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews.index');
    Route::post('/interviews/claim', [InterviewController::class, 'claim'])->name('interviews.claim');
    Route::post('/interviews/{interview}/release', [InterviewController::class, 'release'])->name('interviews.release');
    Route::get('/interviews/create', [InterviewController::class, 'create'])->name('interviews.create');
    Route::post('/interviews', [InterviewController::class, 'store'])->name('interviews.store');
    Route::get('/interviews/{interview}/score', [InterviewController::class, 'showScoreForm'])->name('interviews.score');
    Route::post('/interviews/{interview}/score', [InterviewController::class, 'submitScore'])->name('interviews.score.submit');

    // Rankings & Leaderboard
    Route::get('/rankings/psychotest', [RankingController::class, 'psychotestRankings'])->name('rankings.psychotest');
    Route::get('/rankings/final', [RankingController::class, 'finalRankings'])->name('rankings.final');
    Route::post('/rankings/{candidate}/decision', [RankingController::class, 'updateFinalDecision'])->name('rankings.decision');

    // Reports & Exports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export-psychotest-excel', [ReportController::class, 'exportPsychotestExcel'])->name('reports.export.psychotest');
    Route::get('/reports/candidates/{candidate}/pdf', [ReportController::class, 'exportCandidatePdf'])->name('reports.candidate.pdf');

    // Settings, User Management & Audit Logs
    Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
