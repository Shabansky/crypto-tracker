<?php

namespace Tests\Feature;

use App\Domain\Subscription\Domain\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_success(): void
    {
        $this->postJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 100.00,
            'timeframe' => 1,
        ]);

        $response = $this->get('api/subscription/test@example.com');

        $response->assertStatus(200);
    }

    public function test_post_success(): void
    {
        $subscriptionCountInit = Subscription::where('email', 'test@example.com')->count();

        $response = $this->postJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 100.00,
            'timeframe' => 1,
        ]);

        $subscriptionCountEnd = Subscription::where('email', 'test@example.com')->count();

        $this->assertGreaterThan($subscriptionCountInit, $subscriptionCountEnd);

        $response->assertStatus(200);
        $response->assertContent('Subscription added successfully');
    }

    /**
     * This test holds the entirety of validations that can
     * be encountered in the three endpoints. Thus specific data
     * validations will not be repeated for other endpoints.
     * 
     * The only specific tests are the ones testing for request
     * parameters existence as they are integral to the endpoint's
     * functioning.
     */
    public function test_post_validations_fail(): void
    {
        /* Missing email parameter */
        $response = $this->postJson('api/subscription', [
            'threshold' => 100.00,
            'timeframe' => 1,
        ]);

        $response->assertStatus(400);

        /* Missing threshold parameter */
        $response = $this->postJson('api/subscription', [
            'email' => 'test@example.com',
            'timeframe' => 1,
        ]);

        $response->assertStatus(400);

        /* Missing timeframe parameter */
        $response = $this->postJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 100.00,
        ]);

        $response->assertStatus(400);

        /* Email not proper mail */
        $response = $this->postJson('api/subscription', [
            'email' => 'test.example.com',
            'threshold' => 100.00,
            'timeframe' => 1,
        ]);

        $response->assertStatus(400);

        /* Threshold negative */
        $response = $this->postJson('api/subscription', [
            'email' => 'test.example.com',
            'threshold' => -20.0,
            'timeframe' => 1,
        ]);

        $response->assertStatus(400);

        /* Threshold non-numeric */
        $response = $this->postJson('api/subscription', [
            'email' => 'test.example.com',
            'threshold' => "some percentage",
            'timeframe' => 1,
        ]);

        $response->assertStatus(400);

        /* Threshold precision different than 0,2 */
        $response = $this->postJson('api/subscription', [
            'email' => 'test.example.com',
            'threshold' => 100.000012,
            'timeframe' => 1,
        ]);

        $response->assertStatus(400);

        /* Timeframe non-numeric */
        $response = $this->postJson('api/subscription', [
            'email' => 'test.example.com',
            'threshold' => 100.00,
            'timeframe' => 'one',
        ]);

        $response->assertStatus(400);

        /* Timeframe not in allowed list (1, 6, 24) */
        $response = $this->postJson('api/subscription', [
            'email' => 'test.example.com',
            'threshold' => 100.00,
            'timeframe' => 2,
        ]);

        $response->assertStatus(400);
    }

    public function test_patch_success(): void
    {
        //Initialize subscription as post is not an upsert
        $this->postJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 50.00,
            'timeframe' => 1,
        ]);


        /* With both threshold and timeframe defined */
        $response = $this->patchJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 100.00,
            'timeframe' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertContent('Subscription updated successfully');
    }

    public function test_patch_validations_fail()
    {
        /* Missing email parameter */
        $response = $this->patchJson('api/subscription', [
            'threshold' => 100.00,
            'timeframe' => 1,
        ]);

        $response->assertStatus(400);
        $response->assertContent('Invalid request: The email field is required.');

        /* Only with timeframe defined */
        $response = $this->patchJson('api/subscription', [
            'email' => 'test@example.com',
            'timeframe' => 1,
        ]);

        /* Missing timeframe */
        $response = $this->patchJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 100.00,
        ]);

        $response->assertStatus(400);
        $response->assertContent('Invalid request: The timeframe field is required.');

        /* Missing timeframe and threshold */
        $response = $this->patchJson('api/subscription', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(400);
        $response->assertContent('Invalid request: The threshold field is required. (and 1 more error)');
    }

    public function test_delete_success(): void
    {
        //Initialize subscription
        $this->postJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 50.00,
            'timeframe' => 1,
        ]);

        $subscriptionCountInit = Subscription::where('email', 'test@example.com')->count();

        $response = $this->delete('api/subscription/test@example.com');

        $subscriptionCountEnd = Subscription::where('email', 'test@example.com')->count();
        $this->assertLessThan($subscriptionCountInit, $subscriptionCountEnd);

        $response->assertStatus(200);
        $response->assertContent('Subscription deleted successfully');
    }

    public function test_delete_subscription_setting()
    {
        //Initialize subscription settings for each timeframe
        $this->postJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 50.00,
            'timeframe' => 1,
        ]);

        $this->postJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 50.00,
            'timeframe' => 6,
        ]);

        $this->postJson('api/subscription', [
            'email' => 'test@example.com',
            'threshold' => 50.00,
            'timeframe' => 24,
        ]);

        $subscriptionCountInit = Subscription::where('email', 'test@example.com')->count();

        $this->delete('api/subscription/test@example.com/1');

        $subscriptionCountEnd = Subscription::where('email', 'test@example.com')->count();
        var_dump($subscriptionCountInit, $subscriptionCountEnd);
        $this->assertEquals($subscriptionCountInit - 1, $subscriptionCountEnd);
    }

    public function test_delete_validations_fail()
    {
        /* Missing email parameter */
        $response = $this->postJson('api/subscription', [
            'threshold' => 100.00,
            'timeframe' => 1,
        ]);

        $response->assertStatus(400);
    }
}
