<?php

namespace Tests\Feature\Campaign;

use App\Events\CampaignCompleted;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Draft;
use App\Models\Outcome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CampaignCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_completing_campaign_writes_outcome_per_approved_draft(): void
    {
        Event::fake([CampaignCompleted::class]);

        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);

        // 2 accepted apps with approved drafts (should produce outcomes)
        $accepted = Application::factory()->accepted()->count(2)->create(['campaign_id' => $campaign->id]);
        foreach ($accepted as $app) {
            Draft::factory()->approved()->create(['application_id' => $app->id]);
        }

        // 1 accepted app with NO approved draft (should not produce outcome)
        Application::factory()->accepted()->create(['campaign_id' => $campaign->id]);

        // 1 rejected app (irrelevant)
        Application::factory()->rejected()->create(['campaign_id' => $campaign->id]);

        $response = $this->actingAs($brand)->postJson(route('campaigns.complete', $campaign));

        $response->assertOk()->assertJsonPath('data.state', 'completed');
        $this->assertSame(2, Outcome::where('campaign_id', $campaign->id)->count());

        Event::assertDispatched(CampaignCompleted::class);
    }

    public function test_cannot_complete_already_closed_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->closed()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(route('campaigns.complete', $campaign));

        $response->assertUnprocessable()->assertJsonValidationErrors(['state']);
    }

    public function test_other_brand_cannot_complete_campaign(): void
    {
        $brandA = User::factory()->brand()->create();
        $brandB = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brandB->id]);

        $response = $this->actingAs($brandA)->postJson(route('campaigns.complete', $campaign));

        $response->assertForbidden();
    }

    public function test_recording_outcome_metrics_computes_cpr(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->accepted()->create([
            'campaign_id' => $campaign->id,
            'proposed_price_cents' => 200000,
        ]);
        Draft::factory()->approved()->create(['application_id' => $application->id]);

        // Complete to create outcome
        $this->actingAs($brand)->postJson(route('campaigns.complete', $campaign));
        $outcome = Outcome::where('campaign_id', $campaign->id)->firstOrFail();

        // Influencer records metrics
        $response = $this->actingAs(User::find($application->influencer_id))->postJson(
            route('outcomes.record', $outcome),
            [
                'final_post_url' => 'https://instagram.com/p/123',
                'reach' => 50000,
                'engagement' => 4500,
                'conversions' => 200,
            ]
        );

        $response->assertOk()
            ->assertJsonPath('data.reach', 50000)
            ->assertJsonPath('data.cost_per_result_cents', 1000); // 200000 / 200
    }
}
