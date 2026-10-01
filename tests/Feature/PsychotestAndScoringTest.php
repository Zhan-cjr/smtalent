<?php

namespace Tests\Feature;

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
}
