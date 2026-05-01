<?php

namespace Tests\Feature\Notification;

use App\Actions\Application\AcceptApplication;
use App\Actions\Draft\ApproveDraft;
use App\Actions\Draft\RequestDraftChanges;
use App\Actions\Draft\SubmitDraft;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Draft;
use App\Models\User;
use App\Notifications\ApplicationAcceptedNotification;
use App\Notifications\CampaignCompletedNotification;
use App\Notifications\DraftApprovedNotification;
use App\Notifications\DraftChangesRequestedNotification;
use App\Notifications\DraftSubmittedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationListenersTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepting_application_notifies_influencer(): void
    {
        Notification::fake();

        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]);

        app(AcceptApplication::class)($application, $brand);

        Notification::assertSentTo($influencer, ApplicationAcceptedNotification::class);
    }

    public function test_submitting_draft_notifies_brand(): void
    {
        Notification::fake();

        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->accepted()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]);

        app(SubmitDraft::class)($application, [
            'platform' => 'instagram',
            'file_url' => 'https://example.com/draft.mp4',
        ]);

        Notification::assertSentTo($brand, DraftSubmittedNotification::class);
    }

    public function test_approving_draft_notifies_influencer(): void
    {
        Notification::fake();

        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->accepted()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]);
        $draft = Draft::factory()->create(['application_id' => $application->id]);

        app(ApproveDraft::class)($draft, $brand);

        Notification::assertSentTo($influencer, DraftApprovedNotification::class);
    }

    public function test_requesting_changes_notifies_influencer(): void
    {
        Notification::fake();

        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->accepted()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]);
        $draft = Draft::factory()->create(['application_id' => $application->id]);

        app(RequestDraftChanges::class)($draft, $brand, 'Reshoot please.');

        Notification::assertSentTo($influencer, DraftChangesRequestedNotification::class);
    }

    public function test_completing_campaign_notifies_accepted_influencers(): void
    {
        Notification::fake();

        $brand = User::factory()->brand()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);

        $accepted = User::factory()->count(3)->create();
        foreach ($accepted as $user) {
            $app = Application::factory()->accepted()->create([
                'campaign_id' => $campaign->id,
                'influencer_id' => $user->id,
            ]);
            Draft::factory()->approved()->create(['application_id' => $app->id]);
        }

        $rejectedUser = User::factory()->create();
        Application::factory()->rejected()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $rejectedUser->id,
        ]);

        $this->actingAs($brand)->postJson(route('campaigns.complete', $campaign))->assertOk();

        foreach ($accepted as $user) {
            Notification::assertSentTo($user, CampaignCompletedNotification::class);
        }
        Notification::assertNotSentTo($rejectedUser, CampaignCompletedNotification::class);
    }
}
