<?php

namespace Tests\Feature\Console;

use App\Enums\CampaignState;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Draft;
use App\Models\User;
use App\Notifications\DraftDeadlineReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ScheduledCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_remind_deadlines_notifies_accepted_influencers_with_imminent_deadlines(): void
    {
        Notification::fake();

        $brand = User::factory()->brand()->create();

        $imminent = Campaign::factory()->published()->create([
            'brand_id' => $brand->id,
            'application_deadline' => now()->addHours(12),
        ]);
        $farOff = Campaign::factory()->published()->create([
            'brand_id' => $brand->id,
            'application_deadline' => now()->addDays(7),
        ]);

        $reminded = User::factory()->create();
        Application::factory()->accepted()->create([
            'campaign_id' => $imminent->id,
            'influencer_id' => $reminded->id,
        ]);

        $alreadySubmitted = User::factory()->create();
        $appWithDraft = Application::factory()->accepted()->create([
            'campaign_id' => $imminent->id,
            'influencer_id' => $alreadySubmitted->id,
        ]);
        Draft::factory()->create(['application_id' => $appWithDraft->id]);

        $farOffUser = User::factory()->create();
        Application::factory()->accepted()->create([
            'campaign_id' => $farOff->id,
            'influencer_id' => $farOffUser->id,
        ]);

        $this->artisan('campaigns:remind-deadlines')->assertSuccessful();

        Notification::assertSentTo($reminded, DraftDeadlineReminderNotification::class);
        Notification::assertNotSentTo($alreadySubmitted, DraftDeadlineReminderNotification::class);
        Notification::assertNotSentTo($farOffUser, DraftDeadlineReminderNotification::class);
    }

    public function test_close_expired_closes_past_end_date_campaigns(): void
    {
        $brand = User::factory()->brand()->create();

        $expired = Campaign::factory()->published()->create([
            'brand_id' => $brand->id,
            'ends_on' => now()->subDay(),
        ]);
        $active = Campaign::factory()->published()->create([
            'brand_id' => $brand->id,
            'ends_on' => now()->addWeek(),
        ]);
        $draft = Campaign::factory()->create([
            'brand_id' => $brand->id,
            'ends_on' => now()->subDay(),
        ]);

        $this->artisan('campaigns:close-expired')->assertSuccessful();

        $this->assertSame(CampaignState::Closed, $expired->fresh()->state);
        $this->assertSame(CampaignState::Published, $active->fresh()->state);
        $this->assertSame(CampaignState::Draft, $draft->fresh()->state);
    }
}
