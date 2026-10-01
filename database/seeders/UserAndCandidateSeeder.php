<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\InterviewScore;
use App\Models\Position;
use App\Models\TestAttempt;
use App\Models\TestPackage;
use App\Models\User;
use App\Models\Vacancy;
use App\Services\FinalScoreService;
use App\Services\RankingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserAndCandidateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun HRD / Admin
        $hrd = User::updateOrCreate(
            ['email' => 'hrd@example.com'],
            [
                'name' => 'HRD Rekrutmen & Talent Acquisition',
                'password' => Hash::make('Password123!'),
                'role' => 'hrd',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        // 2. Buat Lowongan untuk masing-masing posisi
        $positions = Position::all();
        $vacancies = [];

        foreach ($positions as $pos) {
            $vacancies[$pos->code] = Vacancy::updateOrCreate(
                ['title' => 'Rekrutmen '.$pos->name.' - Batch 2026'],
                [
                    'position_id' => $pos->id,
                    'description' => 'Dibuka kesempatan bergabung sebagai '.$pos->name.' dengan penempatan area operasional utama.',
                    'quota' => 3,
                    'start_date' => now()->subDays(10)->toDateString(),
                    'end_date' => now()->addDays(20)->toDateString(),
                    'status' => 'open',
                ]
            );
        }

        // 3. Buat Data Dummy Kandidat untuk Barista (untuk uji ranking & tie breaker)
        $baristaVacancy = $vacancies['BAR'];
        $baristaPackage = TestPackage::where('position_id', $positions->firstWhere('code', 'BAR')->id)->first();

        $candidateProfiles = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@kandidat.com',
                'phone' => '081211112222',
                'nik' => '3201112233440001',
                'gender' => 'L',
                'status' => 'DITERIMA',
                'final_status' => 'LOLOS',
                'psycho_score' => 90.00,
                'interview_score' => 88.00,
                'duration' => 2400, // 40 menit
            ],
            [
                'name' => 'Andi Pratama',
                'email' => 'andi@kandidat.com',
                'phone' => '081222223333',
                'nik' => '3201112233440002',
                'gender' => 'L',
                'status' => 'DITERIMA',
                'final_status' => 'LOLOS',
                'psycho_score' => 87.00,
                'interview_score' => 90.00,
                'duration' => 2550, // 42.5 menit
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@kandidat.com',
                'phone' => '081233334444',
                'nik' => '3201112233440003',
                'gender' => 'P',
                'status' => 'CADANGAN',
                'final_status' => 'CADANGAN',
                'psycho_score' => 85.00,
                'interview_score' => 84.00,
                'duration' => 2600,
            ],
            [
                'name' => 'Rina Wijaya',
                'email' => 'rina@kandidat.com',
                'phone' => '081244445555',
                'nik' => '3201112233440004',
                'gender' => 'P',
                'status' => 'TIDAK_LULUS',
                'final_status' => 'TIDAK_LOLOS',
                'psycho_score' => 65.00,
                'interview_score' => null,
                'duration' => 2900,
            ],
            [
                'name' => 'Dimas Anggara',
                'email' => 'dimas@kandidat.com',
                'phone' => '081255556666',
                'nik' => '3201112233440005',
                'gender' => 'L',
                'status' => 'LULUS_PSIKOTES',
                'final_status' => null,
                'psycho_score' => 88.00,
                'interview_score' => null,
                'duration' => 2100,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@kandidat.com',
                'phone' => '081266667777',
                'nik' => '3201112233440006',
                'gender' => 'P',
                'status' => 'REGISTERED',
                'final_status' => null,
                'psycho_score' => null,
                'interview_score' => null,
                'duration' => 0,
            ],
        ];

        $aspectNames = [
            'Komunikasi',
            'Sikap',
            'Motivasi',
            'Pengalaman',
            'Pengetahuan',
            'Problem Solving',
            'Kerja Sama',
            'Kedisiplinan',
        ];

        foreach ($candidateProfiles as $candData) {
            $user = User::updateOrCreate(
                ['email' => $candData['email']],
                [
                    'name' => $candData['name'],
                    'password' => Hash::make('Password123!'),
                    'role' => 'candidate',
                    'phone' => $candData['phone'],
                    'is_active' => true,
                ]
            );

            $candidate = Candidate::updateOrCreate(
                ['email' => $candData['email']],
                [
                    'user_id' => $user->id,
                    'vacancy_id' => $baristaVacancy->id,
                    'name' => $candData['name'],
                    'email' => $candData['email'],
                    'phone' => $candData['phone'],
                    'access_token' => Str::random(40),
                    'nik' => $candData['nik'],
                    'gender' => $candData['gender'],
                    'birth_date' => '2001-05-15',
                    'education' => 'SMA/SMK',
                    'address' => 'Jl. Merdeka No. '.rand(1, 100).', Jakarta',
                    'status' => $candData['status'],
                    'final_psychotest_score' => $candData['psycho_score'],
                    'final_interview_score' => $candData['interview_score'],
                    'final_status' => $candData['final_status'],
                ]
            );

            // Jika ada nilai psikotes, buat record test attempt
            if ($candData['psycho_score'] !== null && $baristaPackage) {
                $started = now()->subDays(3);
                $attempt = TestAttempt::create([
                    'candidate_id' => $candidate->id,
                    'test_package_id' => $baristaPackage->id,
                    'started_at' => $started,
                    'server_end_time' => (clone $started)->addMinutes(50),
                    'submitted_at' => (clone $started)->addSeconds($candData['duration']),
                    'duration_seconds_used' => $candData['duration'],
                    'total_questions' => 40,
                    'total_answered' => 40,
                    'total_correct' => intval(($candData['psycho_score'] / 100) * 40),
                    'total_wrong' => 40 - intval(($candData['psycho_score'] / 100) * 40),
                    'total_unanswered' => 0,
                    'score_multiple_choice' => $candData['psycho_score'],
                    'is_passed' => $candData['psycho_score'] >= 70,
                    'status' => 'completed',
                    'aspect_scores_json' => [
                        'Ketelitian' => ['aspect' => 'Ketelitian', 'average' => 4.4, 'score_100' => 88.0],
                        'Kerja Sama' => ['aspect' => 'Kerja Sama', 'average' => 4.2, 'score_100' => 84.0],
                        'Disiplin' => ['aspect' => 'Disiplin', 'average' => 4.6, 'score_100' => 92.0],
                        'Customer Service' => ['aspect' => 'Customer Service', 'average' => 4.5, 'score_100' => 90.0],
                    ],
                ]);
            }

            // Jika ada nilai interview, buat record interview & scores
            if ($candData['interview_score'] !== null) {
                $interview = Interview::create([
                    'candidate_id' => $candidate->id,
                    'interviewer_id' => $hrd->id,
                    'scheduled_at' => now()->subDays(1)->setHour(10)->setMinute(0),
                    'location' => 'Ruang Interview 1 (Head Office)',
                    'notes' => 'Kandidat memiliki komunikasi yang lugas, percaya diri, dan pemahaman operasional yang matang.',
                    'strengths' => 'Ramah, cepat beradaptasi, berpengalaman mengoperasikan mesin espresso.',
                    'weaknesses' => 'Perlu penyesuaian sedikit pada sistem POS kasir.',
                    'recommendation' => 'DISARANKAN',
                    'average_score' => $candData['interview_score'],
                    'status' => 'completed',
                ]);

                foreach ($aspectNames as $aspect) {
                    InterviewScore::create([
                        'interview_id' => $interview->id,
                        'aspect_name' => $aspect,
                        'score' => $candData['interview_score'] + rand(-3, 3),
                    ]);
                }
            }
        }

        // Jalankan FinalScoreService dan RankingService
        $finalScoreService = app(FinalScoreService::class);
        $finalScoreService->recalculateAllFinalScores();
    }
}
