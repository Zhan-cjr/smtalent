<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PsychotestAndScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_psychotest_and_final_scoring_flow(): void
    {
        $this->seed();

        $candidateUser = User::where('email', 'budi@kandidat.com')->first();
        $this->assertNotNull($candidateUser);

        $response = $this->actingAs($candidateUser)->get(route('home'));
        $response->assertStatus(200);

        // Test login as HRD
        $hrdUser = User::where('email', 'hrd@example.com')->first();
        $this->assertNotNull($hrdUser);

        $hrdResponse = $this->actingAs($hrdUser)->get(route('hrd.dashboard'));
        $hrdResponse->assertStatus(200);

        // Verify psychotest rankings page
        $rankingResponse = $this->actingAs($hrdUser)->get(route('hrd.rankings.psychotest'));
        $rankingResponse->assertStatus(200);

        // Verify final rankings page
        $finalRankingResponse = $this->actingAs($hrdUser)->get(route('hrd.rankings.final'));
        $finalRankingResponse->assertStatus(200);
    }

    public function test_auto_submit_when_time_expires(): void
    {
        $this->seed();

        $candidate = Candidate::first();
        $this->assertNotNull($candidate);

        $attempt = TestAttempt::where('candidate_id', $candidate->id)->first();
        $this->assertNotNull($attempt);

        // Ubah server_end_time menjadi masa lalu (waktu habis)
        $attempt->server_end_time = now()->subMinutes(10);
        $attempt->status = 'in_progress';
        $attempt->save();

        // Akses screen psikotes ketika waktu sudah lewat -> otomatis submit dan redirect ke hasil
        $response = $this->get(route('public.test.screen', ['token' => $candidate->access_token]));
        $response->assertRedirect(route('public.test.result', ['token' => $candidate->access_token]));

        $attempt->refresh();
        $this->assertEquals('completed', $attempt->status);
        $this->assertNotNull($attempt->score_multiple_choice);
        $this->assertNotNull($candidate->fresh()->final_psychotest_score);
    }
}
