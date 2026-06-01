<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_guest_can_view_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('DompetKita')
            ->assertSee('Kelola uang berdua');
    }
}
