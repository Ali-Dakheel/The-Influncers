<?php

namespace Tests\Feature\Campaign;

use App\Enums\CampaignState;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_can_create_campaign(): void
    {
        $brand = User::factory()->brand()->create();

        $response = $this->actingAs($brand)->postJson(route('campaigns.store'), [
            'title' => 'Spring Drop',
            'description' => 'Showcase the spring collection.',
            'category' => 'fashion',
            'platforms' => ['instagram', 'tiktok'],
            'format' => 'reel',
            'objective' => 'engagement',
            'budget_cents' => 500000,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Spring Drop')
            ->assertJsonPath('data.state', 'draft');

        $this->assertDatabaseHas('campaigns', [
            'title' => 'Spring Drop',
            'brand_id' => $brand->id,
            'state' => CampaignState::Draft->value,
        ]);
    }

    public function test_influencer_cannot_create_campaign(): void
    {
        $influencer = User::factory()->create();

        $response = $this->actingAs($influencer)->postJson(route('campaigns.store'), [
            'title' => 'X',
            'description' => 'Y',
            'category' => 'fashion',
            'platforms' => ['instagram'],
            'format' => 'post',
            'objective' => 'awareness',
            'budget_cents' => 1000,
        ]);

        $response->assertForbidden();
    }

    public function test_create_validates_required_fields(): void
    {
        $brand = User::factory()->brand()->create();

        $response = $this->actingAs($brand)->postJson(route('campaigns.store'), []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title', 'description', 'category', 'platforms', 'format', 'objective', 'budget_cents',
            ]);
    }

    public function test_create_validates_enum_values(): void
    {
        $brand = User::factory()->brand()->create();

        $response = $this->actingAs($brand)->postJson(route('campaigns.store'), [
            'title' => 'X',
            'description' => 'Y',
            'category' => 'not-a-category',
            'platforms' => ['myspace'],
            'format' => 'newspaper',
            'objective' => 'world_domination',
            'budget_cents' => 100,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['category', 'platforms.0', 'format', 'objective']);
    }

    public function test_brand_sees_only_own_campaigns_in_index(): void
    {
        $brandA = User::factory()->brand()->create();
        $brandB = User::factory()->brand()->create();
        Campaign::factory()->count(2)->create(['brand_id' => $brandA->id]);
        Campaign::factory()->count(3)->create(['brand_id' => $brandB->id]);

        $response = $this->actingAs($brandA)->getJson(route('campaigns.index'));

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_influencer_sees_only_published_campaigns_in_index(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();

        Campaign::factory()->count(2)->create(['brand_id' => $brand->id]); // drafts
        Campaign::factory()->published()->count(3)->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($influencer)->getJson(route('campaigns.index'));

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_brand_can_update_own_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->patchJson(
            route('campaigns.update', $campaign),
            ['title' => 'Updated Title']
        );

        $response->assertOk()->assertJsonPath('data.title', 'Updated Title');
    }

    public function test_brand_cannot_update_other_brand_campaign(): void
    {
        $brandA = User::factory()->brand()->create();
        $brandB = User::factory()->brand()->create();
        $campaign = Campaign::factory()->create(['brand_id' => $brandB->id]);

        $response = $this->actingAs($brandA)->patchJson(
            route('campaigns.update', $campaign),
            ['title' => 'Hacked']
        );

        $response->assertForbidden();
    }

    public function test_influencer_cannot_view_draft_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($influencer)->getJson(route('campaigns.show', $campaign));

        $response->assertForbidden();
    }

    public function test_influencer_can_view_published_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($influencer)->getJson(route('campaigns.show', $campaign));

        $response->assertOk()->assertJsonPath('data.id', $campaign->id);
    }

    public function test_brand_can_delete_draft_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->deleteJson(route('campaigns.destroy', $campaign));

        $response->assertNoContent();
        $this->assertDatabaseMissing('campaigns', ['id' => $campaign->id]);
    }

    public function test_brand_cannot_delete_published_campaign(): void
    {
        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($brand)->deleteJson(route('campaigns.destroy', $campaign));

        $response->assertForbidden();
    }
}
