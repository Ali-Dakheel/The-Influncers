<?php

namespace Tests\Feature\Campaign;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_can_publish_draft_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('campaigns.publish', $campaign));

        $response->assertOk()->assertJsonPath('data.state', 'published');
        $this->assertNotNull($campaign->fresh()->published_at);
    }

    public function test_publishing_already_published_campaign_fails(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('campaigns.publish', $campaign));

        $response->assertUnprocessable()->assertJsonValidationErrors(['state']);
    }

    public function test_brand_can_pause_published_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('campaigns.pause', $campaign));

        $response->assertOk()->assertJsonPath('data.state', 'paused');
        $this->assertNotNull($campaign->fresh()->paused_at);
    }

    public function test_pausing_draft_campaign_fails(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('campaigns.pause', $campaign));

        $response->assertUnprocessable()->assertJsonValidationErrors(['state']);
    }

    public function test_brand_can_republish_paused_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->paused()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('campaigns.publish', $campaign));

        $response->assertOk()->assertJsonPath('data.state', 'published');
        $this->assertNull($campaign->fresh()->paused_at);
    }

    public function test_brand_can_close_active_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('campaigns.close', $campaign));

        $response->assertOk()->assertJsonPath('data.state', 'closed');
        $this->assertNotNull($campaign->fresh()->closed_at);
    }

    public function test_closing_already_closed_campaign_fails(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->closed()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('campaigns.close', $campaign));

        $response->assertUnprocessable()->assertJsonValidationErrors(['state']);
    }

    public function test_other_brand_cannot_transition_state(): void
    {
        $brandA = User::factory()->brand()->create();
        $brandB = User::factory()->brand()->create();
        $campaign = Campaign::factory()->create(['brand_id' => $brandB->id]);

        $response = $this->actingAs($brandA)->postJson(route('campaigns.publish', $campaign));

        $response->assertForbidden();
    }
}
