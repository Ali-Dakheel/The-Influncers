<?php

namespace Tests\Feature\Reputation;

use App\Enums\CampaignState;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    private function completedScenario(): array
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->state(['state' => CampaignState::Completed])->create(['brand_id' => $brand->id]);
        $application = Application::factory()->accepted()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]);

        return [$brand, $influencer, $campaign, $application];
    }

    public function test_brand_can_rate_influencer_after_campaign_completes(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->completedScenario();

        $response = $this->actingAs($brand)->postJson(route('applications.rate', $application), [
            'score' => 5,
            'text' => 'Outstanding work, on time and on brief.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.score', 5)
            ->assertJsonPath('data.influencer_id', $influencer->id);

        $this->assertDatabaseHas('ratings', [
            'campaign_id' => $campaign->id,
            'brand_id' => $brand->id,
            'influencer_id' => $influencer->id,
            'score' => 5,
        ]);
    }

    public function test_cannot_rate_active_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->accepted()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]);

        $response = $this->actingAs($brand)->postJson(route('applications.rate', $application), ['score' => 4]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['campaign']);
    }

    public function test_other_brand_cannot_rate(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->completedScenario();
        $strangerBrand = User::factory()->brand()->create();

        $response = $this->actingAs($strangerBrand)->postJson(route('applications.rate', $application), ['score' => 5]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['brand']);
    }

    public function test_score_must_be_between_1_and_5(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->completedScenario();

        $response = $this->actingAs($brand)->postJson(route('applications.rate', $application), ['score' => 7]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['score']);
    }

    public function test_re_rating_updates_existing_rating(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->completedScenario();

        $this->actingAs($brand)->postJson(route('applications.rate', $application), ['score' => 3]);
        $response = $this->actingAs($brand)->postJson(route('applications.rate', $application), ['score' => 5, 'text' => 'Updated.']);

        $response->assertCreated()->assertJsonPath('data.score', 5);
        $this->assertSame(1, Rating::count());
    }

    public function test_anyone_can_view_influencer_ratings(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->completedScenario();
        $this->actingAs($brand)->postJson(route('applications.rate', $application), ['score' => 4]);

        $someUser = User::factory()->create();
        $response = $this->actingAs($someUser)->getJson(route('users.ratings', $influencer));

        $response->assertOk()->assertJsonCount(1, 'data');
    }
}
