<?php

namespace Tests\Feature\Creator;

use App\Enums\CampaignState;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Outcome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarAndIncomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_calendar_shows_own_campaigns(): void
    {
        $brand = User::factory()->brand()->create();
        Campaign::factory()->published()->count(2)->create([
            'brand_id' => $brand->id,
            'starts_on' => now(),
            'ends_on' => now()->addWeek(),
            'application_deadline' => now()->addDays(2),
        ]);

        $response = $this->actingAs($brand)->getJson(route('calendar.mine'));

        $response->assertOk();
        $this->assertGreaterThan(0, count($response->json('data')));
    }

    public function test_influencer_calendar_only_shows_accepted_campaigns(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();

        $campaign = Campaign::factory()->published()->create([
            'brand_id' => $brand->id,
            'starts_on' => now(),
        ]);
        Application::factory()->accepted()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]);

        // Unrelated campaign
        Campaign::factory()->published()->create(['brand_id' => $brand->id, 'starts_on' => now()]);

        $response = $this->actingAs($influencer)->getJson(route('calendar.mine'));

        $events = collect($response->assertOk()->json('data'));
        $campaignIds = $events->pluck('campaign_id')->unique();
        $this->assertTrue($campaignIds->contains($campaign->id));
        $this->assertCount(1, $campaignIds);
    }

    public function test_income_summary_aggregates_completed_and_pending(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();

        $completed = Campaign::factory()->state(['state' => CampaignState::Completed])->create(['brand_id' => $brand->id]);
        Application::factory()->accepted()->create([
            'campaign_id' => $completed->id,
            'influencer_id' => $influencer->id,
            'proposed_price_cents' => 200000,
        ]);

        $active = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        Application::factory()->accepted()->create([
            'campaign_id' => $active->id,
            'influencer_id' => $influencer->id,
            'proposed_price_cents' => 100000,
        ]);

        $rejected = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        Application::factory()->rejected()->create([
            'campaign_id' => $rejected->id,
            'influencer_id' => $influencer->id,
            'proposed_price_cents' => 999999,
        ]);

        $response = $this->actingAs($influencer)->getJson(route('income.summary'));

        $response->assertOk()
            ->assertJsonPath('data.earned_cents', 200000)
            ->assertJsonPath('data.pending_cents', 100000)
            ->assertJsonPath('data.completed_campaigns', 1)
            ->assertJsonPath('data.active_campaigns', 1);
    }

    public function test_income_summary_includes_lifetime_engagement(): void
    {
        $influencer = User::factory()->create();
        Outcome::factory()->recorded()->count(2)->create([
            'influencer_id' => $influencer->id,
            'reach' => 10000,
            'engagement' => 500,
            'conversions' => 50,
        ]);

        $response = $this->actingAs($influencer)->getJson(route('income.summary'));

        $response->assertOk()
            ->assertJsonPath('data.lifetime_reach', 20000)
            ->assertJsonPath('data.lifetime_engagement', 1000)
            ->assertJsonPath('data.lifetime_conversions', 100);
    }
}
