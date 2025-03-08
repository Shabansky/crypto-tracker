<?php

namespace Tests\Feature;

use Tests\TestCase;

class EndpointsTest extends TestCase
{
    public function test_post_success(): void
    {
        $response = $this->postJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 100.00,
            'timeframe' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertContent('New Subscription');
    }

    public function test_patch_success(): void
    {
        /* With both threshold and timeframe defined */
        $response = $this->patchJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 100.00,
            'timeframe' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertContent('Edit Existing Subscription');

        /* Only with timeframe defined */
        $response = $this->patchJson('api/subscription', [
            'email' => 'test@example.com',
            'timeframe' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertContent('Edit Existing Subscription');

        /* Only with threshold defined */
        $response = $this->patchJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 100.00,
        ]);

        $response->assertStatus(200);
        $response->assertContent('Edit Existing Subscription');

        /* With neither threshold nor timeframe defined */
        $response = $this->patchJson('api/subscription', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertContent('Edit Existing Subscription');
    }

    public function test_delete_success(): void
    {
        $response = $this->deleteJson('api/subscription', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertContent('Delete Existing Subscription');
    }
}
