<?php

namespace Tests\Feature\Payment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeOnboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_influencer_can_start_onboarding(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('stripe.onboard.start'));

        $response->assertOk()
            ->assertJsonStructure(['account_id', 'onboarding_url', 'onboarded']);

        $this->assertNotNull($user->fresh()->stripe_account_id);
        $this->assertFalse($user->fresh()->stripe_onboarded);
    }

    public function test_brand_cannot_onboard_to_stripe_connect(): void
    {
        $brand = User::factory()->brand()->create();

        $response = $this->actingAs($brand)->postJson(route('stripe.onboard.start'));

        $response->assertForbidden();
    }

    public function test_complete_onboarding_after_start(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('stripe.onboard.start'));
        $response = $this->actingAs($user)->postJson(route('stripe.onboard.complete'));

        $response->assertOk()->assertJsonPath('onboarded', true);
        $this->assertTrue($user->fresh()->stripe_onboarded);
    }

    public function test_cannot_complete_without_starting(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('stripe.onboard.complete'));

        $response->assertUnprocessable();
    }
}
