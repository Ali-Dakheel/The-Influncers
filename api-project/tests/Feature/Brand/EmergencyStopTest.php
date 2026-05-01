<?php

namespace Tests\Feature\Brand;

use App\Enums\CampaignState;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\User;
use App\Notifications\CampaignEmergencyPausedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmergencyStopTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_can_emergency_stop_their_published_campaigns(): void
    {
        Notification::fake();

        $brand = User::factory()->brand()->create();
        $otherBrand = User::factory()->brand()->create();

        $own1 = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $own2 = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $ownDraft = Campaign::factory()->create(['brand_id' => $brand->id]); // not published, not affected
        $otherCampaign = Campaign::factory()->published()->create(['brand_id' => $otherBrand->id]);

        $influencer = User::factory()->create();
        Application::factory()->accepted()->create([
            'campaign_id' => $own1->id,
            'influencer_id' => $influencer->id,
        ]);

        $response = $this->actingAs($brand)->postJson(route('brand.emergency-stop'), [
            'reason' => 'Brand crisis — investigating.',
        ]);

        $response->assertOk()->assertJsonPath('paused_count', 2);

        $this->assertSame(CampaignState::Paused, $own1->fresh()->state);
        $this->assertSame(CampaignState::Paused, $own2->fresh()->state);
        $this->assertSame(CampaignState::Draft, $ownDraft->fresh()->state);
        $this->assertSame(CampaignState::Published, $otherCampaign->fresh()->state);

        Notification::assertSentTo($influencer, CampaignEmergencyPausedNotification::class);
    }

    public function test_influencer_cannot_emergency_stop(): void
    {
        $influencer = User::factory()->create();

        $response = $this->actingAs($influencer)->postJson(route('brand.emergency-stop'));

        $response->assertForbidden();
    }
}
