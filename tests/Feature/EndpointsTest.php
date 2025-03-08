<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EndpointsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('api/');

        $response->assertStatus(404);
    }

    public function test_post(): void
    {
        $response = $this->post('api/subscription');

        $response->assertStatus(200);
        $response->assertContent('New Subscription');
    }

    public function test_patch(): void
    {
        $response = $this->patch('api/subscription');

        $response->assertStatus(200);
        $response->assertContent('Edit Existing Subscription');
    }

    public function test_delete(): void
    {
        $response = $this->delete('api/subscription');

        $response->assertStatus(200);
        $response->assertContent('Delete Existing Subscription');
    }
}
