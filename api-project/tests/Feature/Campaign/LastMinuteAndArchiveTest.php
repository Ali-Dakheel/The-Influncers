<?php

namespace Tests\Feature\Campaign;

use App\Enums\CampaignState;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Draft;
use App\Models\User;
use App\Notifications\CampaignDateChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LastMinuteAndArchiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_last_minute_endpoint_returns_only_urgent_published(): void
    {
        $brand = User::factory()->brand()->create();
        $user = User::factory()->create();

        Campaign::factory()->published()->create([
            'brand_id' => $brand->id,
            'is_urgent' => true,
            'urgent_expires_at' => now()->addHours(6),
            'title' => 'Urgent A',
        ]);
        Campaign::factory()->published()->create(['brand_id' => $brand->id, 'is_urgent' => false, 'title' => 'Normal']);
        Campaign::factory()->create(['brand_id' => $brand->id, 'is_urgent' => true, 'title' => 'Urgent Draft']);

        $response = $this->actingAs($user)->getJson(route('campaigns.last-minute'));

        $response->assertOk();
        $titles = collect($response->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('Urgent A'));
        $this->assertFalse($titles->contains('Normal'));
        $this->assertFalse($titles->contains('Urgent Draft'));
    }

    public function test_changing_campaign_dates_fires_notification_to_accepted_influencers(): void
    {
        Notification::fake();

        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create([
            'brand_id' => $brand->id,
            'starts_on' => now()->addWeek(),
        ]);
        Application::factory()->accepted()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]);

        $campaign->update(['starts_on' => now()->addWeeks(2)]);

        Notification::assertSentTo($influencer, CampaignDateChangedNotification::class);
    }

    public function test_archive_command_archives_old_completed_campaigns(): void
    {
        $brand = User::factory()->brand()->create();

        $oldCompleted = Campaign::factory()->state([
            'state' => CampaignState::Completed,
            'completed_at' => now()->subDays(120),
            'brand_id' => $brand->id,
        ])->create();
        $recentCompleted = Campaign::factory()->state([
            'state' => CampaignState::Completed,
            'completed_at' => now()->subDays(10),
            'brand_id' => $brand->id,
        ])->create();
        $oldDraft = Campaign::factory()->create(['brand_id' => $brand->id]);

        $app = Application::factory()->accepted()->create(['campaign_id' => $oldCompleted->id]);
        Draft::factory()->approved()->create(['application_id' => $app->id]);

        $this->artisan('content:archive')->assertSuccessful();

        $this->assertNotNull($oldCompleted->fresh()->archived_at);
        $this->assertNull($recentCompleted->fresh()->archived_at);
        $this->assertNull($oldDraft->fresh()->archived_at);
    }
}
