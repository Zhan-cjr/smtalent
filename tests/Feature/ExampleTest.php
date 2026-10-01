<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public job portal landing returns successful response.
     */
    public function test_root_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SM');
        $response->assertSee('Talent');
        $response->assertSee('ZhanSoft');
    }
}
