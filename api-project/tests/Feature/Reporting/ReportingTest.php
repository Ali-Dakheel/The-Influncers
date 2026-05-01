<?php

namespace Tests\Feature\Reporting;

use App\Models\Application;
use App\Models\Campaign;
use App\Models\Country;
use App\Models\Outcome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_all_outcomes(): void
    {
        $admin = User::factory()->admin()->create();
        Outcome::factory()->recorded()->count(3)->create(['reach' => 1000]);

        $response = $this->actingAs($admin)->getJson(route('reports.aggregate'));

        $response->assertOk()
            ->assertJsonPath('data.totals.campaign_count', 3)
            ->assertJsonPath('data.totals.reach', 3000);
    }

    public function test_country_scoped_admin_sees_only_their_country(): void
    {
        $us = Country::factory()->create(['code' => 'US']);
        $gb = Country::factory()->create(['code' => 'GB']);

        $countryAdmin = User::factory()->admin()->create(['country_id' => $us->id]);

        Outcome::factory()->recorded()->count(2)->create(['country_id' => $us->id, 'reach' => 5000]);
        Outcome::factory()->recorded()->count(3)->create(['country_id' => $gb->id, 'reach' => 9000]);

        $response = $this->actingAs($countryAdmin)->getJson(route('reports.aggregate'));

        $response->assertOk()
            ->assertJsonPath('data.totals.campaign_count', 2)
            ->assertJsonPath('data.totals.reach', 10000);
    }

    public function test_brand_sees_only_own_campaign_outcomes(): void
    {
        $brand = User::factory()->brand()->create();
        $otherBrand = User::factory()->brand()->create();

        $myCampaign = Campaign::factory()->create(['brand_id' => $brand->id]);
        $otherCampaign = Campaign::factory()->create(['brand_id' => $otherBrand->id]);

        for ($i = 0; $i < 2; $i++) {
            $app = Application::factory()->accepted()->create(['campaign_id' => $myCampaign->id]);
            Outcome::factory()->recorded()->create([
                'campaign_id' => $myCampaign->id,
                'application_id' => $app->id,
                'influencer_id' => $app->influencer_id,
            ]);
        }
        for ($i = 0; $i < 5; $i++) {
            $app = Application::factory()->accepted()->create(['campaign_id' => $otherCampaign->id]);
            Outcome::factory()->recorded()->create([
                'campaign_id' => $otherCampaign->id,
                'application_id' => $app->id,
                'influencer_id' => $app->influencer_id,
            ]);
        }

        $response = $this->actingAs($brand)->getJson(route('reports.aggregate'));

        $response->assertOk()->assertJsonPath('data.totals.campaign_count', 2);
    }

    public function test_influencer_sees_only_own_outcomes(): void
    {
        $influencer = User::factory()->create();

        Outcome::factory()->recorded()->count(2)->create(['influencer_id' => $influencer->id, 'reach' => 1000]);
        Outcome::factory()->recorded()->count(4)->create(['reach' => 999]);

        $response = $this->actingAs($influencer)->getJson(route('reports.aggregate'));

        $response->assertOk()
            ->assertJsonPath('data.totals.campaign_count', 2)
            ->assertJsonPath('data.totals.reach', 2000);
    }

    public function test_filters_apply(): void
    {
        $admin = User::factory()->admin()->create();

        Outcome::factory()->recorded()->create(['platform' => 'instagram', 'reach' => 1000]);
        Outcome::factory()->recorded()->create(['platform' => 'tiktok', 'reach' => 5000]);

        $response = $this->actingAs($admin)->getJson(route('reports.aggregate', ['platform' => 'instagram']));

        $response->assertOk()
            ->assertJsonPath('data.totals.campaign_count', 1)
            ->assertJsonPath('data.totals.reach', 1000);
    }

    public function test_export_returns_json_attachment(): void
    {
        $admin = User::factory()->admin()->create();
        Outcome::factory()->recorded()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('reports.export'));

        $response->assertOk();
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }
}
