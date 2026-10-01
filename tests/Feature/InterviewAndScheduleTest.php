<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\TestAttempt;
use App\Models\TestPackage;
use App\Models\TestSchedule;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InterviewAndScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_candidate_cannot_start_test_if_no_schedule_exists(): void
    {
        // Pastikan tidak ada jadwal untuk vacancy
        TestSchedule::query()->delete();

        $vacancy = Vacancy::first();
        $this->assertNotNull($vacancy);

        // Akses halaman home
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal Tes Belum Dibuat');

        // Coba submit start test
        $startResponse = $this->post(route('public.test.start'), [
            'vacancy_id' => $vacancy->id,
            'name' => 'Kandidat Uji',
            'email' => 'uji@kandidat.com',
            'phone' => '081299990000',
            'gender' => 'L',
        ]);

        $startResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('candidates', [
            'email' => 'uji@kandidat.com',
            'status' => 'PSIKOTES',
        ]);
    }

    public function test_candidate_can_start_test_when_schedule_is_active(): void
    {
        $vacancy = Vacancy::first();
        $package = TestPackage::where('position_id', $vacancy->position_id)->first()
            ?? TestPackage::first();

        // Buat jadwal aktif sekarang
        TestSchedule::create([
            'test_package_id' => $package->id,
            'name' => 'Sesi Aktif Sekarang',
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(2),
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Sesi Aktif Sekarang');
        $response->assertSee('Sesi dibuka');

        $startResponse = $this->post(route('public.test.start'), [
            'vacancy_id' => $vacancy->id,
            'name' => 'Kandidat Aktif',
            'email' => 'aktif@kandidat.com',
            'phone' => '081299991111',
            'gender' => 'L',
        ]);

        $startResponse->assertRedirect();
        $this->assertDatabaseHas('candidates', [
            'email' => 'aktif@kandidat.com',
        ]);
    }

    public function test_interview_candidates_ordered_by_first_completed_psychotest(): void
    {
        $hrd = User::where('role', 'hrd')->first();
        $vacancy = Vacancy::first();
        $package = TestPackage::where('position_id', $vacancy->position_id)->first()
            ?? TestPackage::first();

        // Buat 2 kandidat baru dengan waktu selesai berbeda
        $cand1 = Candidate::create([
            'vacancy_id' => $vacancy->id,
            'name' => 'Kandidat Pertama Selesai',
            'email' => 'pertama@kandidat.com',
            'phone' => '081200000001',
            'access_token' => Str::random(40),
            'gender' => 'L',
            'status' => 'LULUS_PSIKOTES',
            'final_psychotest_score' => 85.00,
        ]);

        TestAttempt::create([
            'candidate_id' => $cand1->id,
            'test_package_id' => $package->id,
            'started_at' => now()->subHours(5),
            'server_end_time' => now()->subHours(4),
            'submitted_at' => now()->subHours(4)->subMinutes(30), // Selesai lebih awal
            'status' => 'completed',
            'score_multiple_choice' => 85.00,
        ]);

        $cand2 = Candidate::create([
            'vacancy_id' => $vacancy->id,
            'name' => 'Kandidat Kedua Selesai',
            'email' => 'kedua@kandidat.com',
            'phone' => '081200000002',
            'access_token' => Str::random(40),
            'gender' => 'L',
            'status' => 'LULUS_PSIKOTES',
            'final_psychotest_score' => 90.00,
        ]);

        TestAttempt::create([
            'candidate_id' => $cand2->id,
            'test_package_id' => $package->id,
            'started_at' => now()->subHours(3),
            'server_end_time' => now()->subHours(2),
            'submitted_at' => now()->subHours(2)->subMinutes(10), // Selesai lebih lambat
            'status' => 'completed',
            'score_multiple_choice' => 90.00,
        ]);

        $response = $this->actingAs($hrd)->get(route('hrd.interviews.index', ['tab' => 'candidates']));
        $response->assertStatus(200);

        // Kandidat yang pertama selesai harus muncul sebelum kandidat yang kedua selesai
        $content = $response->getContent();
        $pos1 = strpos($content, 'Kandidat Pertama Selesai');
        $pos2 = strpos($content, 'Kandidat Kedua Selesai');

        $this->assertNotFalse($pos1);
        $this->assertNotFalse($pos2);
        $this->assertTrue($pos1 < $pos2, 'Kandidat yang pertama selesai harus muncul lebih awal/atas.');
    }

    public function test_multi_hrd_interview_locking_and_claim_protection(): void
    {
        // Buat 2 akun HRD
        $hrd1 = User::create([
            'name' => 'HRD Satu',
            'email' => 'hrd1@example.com',
            'password' => bcrypt('password'),
            'role' => 'hrd',
            'is_active' => true,
        ]);

        $hrd2 = User::create([
            'name' => 'HRD Dua',
            'email' => 'hrd2@example.com',
            'password' => bcrypt('password'),
            'role' => 'hrd',
            'is_active' => true,
        ]);

        $vacancy = Vacancy::first();
        $candidate = Candidate::create([
            'vacancy_id' => $vacancy->id,
            'name' => 'Kandidat Rebutan',
            'email' => 'rebutan@kandidat.com',
            'phone' => '081299998888',
            'access_token' => Str::random(40),
            'gender' => 'L',
            'status' => 'LULUS_PSIKOTES',
            'final_psychotest_score' => 88.00,
        ]);

        // HRD 1 memilih kandidat untuk interview
        $claimResponse1 = $this->actingAs($hrd1)->post(route('hrd.interviews.claim'), [
            'candidate_id' => $candidate->id,
        ]);

        $claimResponse1->assertRedirect();
        $this->assertDatabaseHas('interviews', [
            'candidate_id' => $candidate->id,
            'interviewer_id' => $hrd1->id,
            'status' => 'in_progress',
        ]);

        // HRD 2 mencoba memilih kandidat yang sama -> HARUS DITOLAK
        $claimResponse2 = $this->actingAs($hrd2)->post(route('hrd.interviews.claim'), [
            'candidate_id' => $candidate->id,
        ]);

        $claimResponse2->assertSessionHas('error');

        // Pastikan interviewer tetap HRD 1
        $interview = Interview::where('candidate_id', $candidate->id)->first();
        $this->assertEquals($hrd1->id, $interview->interviewer_id);

        // HRD 2 juga tidak boleh mengakses lembar penilaian kandidat ini
        $scorePageResponse = $this->actingAs($hrd2)->get(route('hrd.interviews.score', $interview->id));
        $scorePageResponse->assertRedirect(route('hrd.interviews.index'));
        $scorePageResponse->assertSessionHas('error');

        // HRD 1 bisa mengakses lembar penilaian
        $scorePageResponseHrd1 = $this->actingAs($hrd1)->get(route('hrd.interviews.score', $interview->id));
        $scorePageResponseHrd1->assertStatus(200);

        // HRD 1 melepas kandidat
        $releaseResponse = $this->actingAs($hrd1)->post(route('hrd.interviews.release', $interview->id));
        $releaseResponse->assertSessionHas('success');

        // Sekarang HRD 2 bisa memilih kandidat tersebut
        $claimResponse2Success = $this->actingAs($hrd2)->post(route('hrd.interviews.claim'), [
            'candidate_id' => $candidate->id,
        ]);
        $claimResponse2Success->assertRedirect();

        $newInterview = Interview::where('candidate_id', $candidate->id)->first();
        $this->assertEquals($hrd2->id, $newInterview->interviewer_id);
    }
}
