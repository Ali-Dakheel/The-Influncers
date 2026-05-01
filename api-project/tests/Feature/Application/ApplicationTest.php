<?php

namespace Tests\Feature\Application;

use App\Enums\ApplicationStatus;
use App\Events\ApplicationAccepted;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_influencer_can_apply_to_published_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($influencer)->postJson(
            route('campaigns.apply', $campaign),
            ['pitch' => 'I would love to work on this!', 'proposed_price_cents' => 200000]
        );

        $response->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.proposed_price_cents', 200000);

        $this->assertDatabaseHas('applications', [
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
            'status' => ApplicationStatus::Pending->value,
        ]);
    }

    public function test_influencer_cannot_apply_to_draft_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($influencer)->postJson(
            route('campaigns.apply', $campaign),
            ['pitch' => 'Hi there I am interested.']
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['campaign']);
    }

    public function test_influencer_cannot_apply_twice(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        Application::factory()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]);

        $response = $this->actingAs($influencer)->postJson(
            route('campaigns.apply', $campaign),
            ['pitch' => 'Round two!']
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['campaign']);
    }

    public function test_brand_cannot_apply_to_their_own_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->postJson(
            route('campaigns.apply', $campaign),
            ['pitch' => 'Self-application attempt.']
        );

        $response->assertForbidden();
    }

    public function test_pitch_must_be_at_least_10_chars(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($influencer)->postJson(
            route('campaigns.apply', $campaign),
            ['pitch' => 'short']
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['pitch']);
    }

    public function test_brand_can_list_applications_for_their_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        Application::factory()->count(3)->create(['campaign_id' => $campaign->id]);

        $response = $this->actingAs($brand)->getJson(route('campaigns.applications.index', $campaign));

        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_brand_cannot_list_applications_for_other_brands_campaign(): void
    {
        $brandA = User::factory()->brand()->create();
        $brandB = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brandB->id]);
        Application::factory()->count(2)->create(['campaign_id' => $campaign->id]);

        $response = $this->actingAs($brandA)->getJson(route('campaigns.applications.index', $campaign));

        $response->assertForbidden();
    }

    public function test_influencer_can_list_their_own_applications(): void
    {
        $influencer = User::factory()->create();
        $other = User::factory()->create();
        Application::factory()->count(2)->create(['influencer_id' => $influencer->id]);
        Application::factory()->count(3)->create(['influencer_id' => $other->id]);

        $response = $this->actingAs($influencer)->getJson(route('applications.mine'));

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_brand_can_accept_pending_application(): void
    {
        Event::fake([ApplicationAccepted::class]);

        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->create(['campaign_id' => $campaign->id]);

        $response = $this->actingAs($brand)->postJson(
            route('applications.accept', $application),
            ['note' => 'Great fit.']
        );

        $response->assertOk()->assertJsonPath('data.status', 'accepted');
        $this->assertSame($brand->id, $application->fresh()->decided_by);

        Event::assertDispatched(ApplicationAccepted::class, fn ($e) => $e->application->id === $application->id);
    }

    public function test_brand_can_reject_pending_application(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->create(['campaign_id' => $campaign->id]);

        $response = $this->actingAs($brand)->postJson(
            route('applications.reject', $application),
            ['note' => 'Not a fit.']
        );

        $response->assertOk()->assertJsonPath('data.status', 'rejected');
    }

    public function test_cannot_accept_already_accepted_application(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->accepted()->create(['campaign_id' => $campaign->id]);

        $response = $this->actingAs($brand)->postJson(route('applications.accept', $application));

        $response->assertUnprocessable()->assertJsonValidationErrors(['status']);
    }

    public function test_other_brand_cannot_decide_application(): void
    {
        $brandA = User::factory()->brand()->create();
        $brandB = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brandB->id]);
        $application = Application::factory()->create(['campaign_id' => $campaign->id]);

        $response = $this->actingAs($brandA)->postJson(route('applications.accept', $application));

        $response->assertForbidden();
    }

    public function test_influencer_can_view_own_application(): void
    {
        $influencer = User::factory()->create();
        $application = Application::factory()->create(['influencer_id' => $influencer->id]);

        $response = $this->actingAs($influencer)->getJson(route('applications.show', $application));

        $response->assertOk()->assertJsonPath('data.id', $application->id);
    }

    public function test_unrelated_user_cannot_view_application(): void
    {
        $unrelated = User::factory()->create();
        $application = Application::factory()->create();

        $response = $this->actingAs($unrelated)->getJson(route('applications.show', $application));

        $response->assertForbidden();
    }
}
