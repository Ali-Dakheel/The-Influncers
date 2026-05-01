<?php

namespace Tests\Feature\Draft;

use App\Enums\DraftStatus;
use App\Events\DraftApproved;
use App\Events\DraftChangesRequested;
use App\Events\DraftSubmitted;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Draft;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DraftTest extends TestCase
{
    use RefreshDatabase;

    private function preparedAcceptedApplication(): array
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->accepted()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]);

        return [$brand, $influencer, $campaign, $application];
    }

    public function test_influencer_can_submit_first_draft(): void
    {
        Event::fake([DraftSubmitted::class]);

        [$brand, $influencer, $campaign, $application] = $this->preparedAcceptedApplication();

        $response = $this->actingAs($influencer)->postJson(
            route('drafts.submit', $application),
            ['platform' => 'instagram', 'file_url' => 'https://example.com/draft.mp4', 'caption' => 'Drop link incoming.']
        );

        $response->assertCreated()
            ->assertJsonPath('data.revision_number', 1)
            ->assertJsonPath('data.status', 'submitted');

        Event::assertDispatched(DraftSubmitted::class);
    }

    public function test_cannot_submit_draft_for_non_accepted_application(): void
    {
        $brand = User::factory()->brand()->create();
        $influencer = User::factory()->create();
        $campaign = Campaign::factory()->published()->create(['brand_id' => $brand->id]);
        $application = Application::factory()->create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
        ]); // pending

        $response = $this->actingAs($influencer)->postJson(
            route('drafts.submit', $application),
            ['platform' => 'instagram', 'file_url' => 'https://example.com/x.mp4']
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['application']);
    }

    public function test_brand_cannot_submit_draft(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->preparedAcceptedApplication();

        $response = $this->actingAs($brand)->postJson(
            route('drafts.submit', $application),
            ['platform' => 'instagram', 'file_url' => 'https://example.com/x.mp4']
        );

        $response->assertForbidden();
    }

    public function test_cannot_submit_revision_when_draft_pending(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->preparedAcceptedApplication();
        Draft::factory()->create(['application_id' => $application->id, 'revision_number' => 1]);

        $response = $this->actingAs($influencer)->postJson(
            route('drafts.submit', $application),
            ['platform' => 'instagram', 'file_url' => 'https://example.com/v2.mp4']
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['draft']);
    }

    public function test_can_submit_revision_after_changes_requested(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->preparedAcceptedApplication();
        Draft::factory()->changesRequested()->create([
            'application_id' => $application->id,
            'revision_number' => 1,
        ]);

        $response = $this->actingAs($influencer)->postJson(
            route('drafts.submit', $application),
            ['platform' => 'instagram', 'file_url' => 'https://example.com/v2.mp4']
        );

        $response->assertCreated()->assertJsonPath('data.revision_number', 2);
    }

    public function test_brand_can_approve_submitted_draft(): void
    {
        Event::fake([DraftApproved::class]);

        [$brand, $influencer, $campaign, $application] = $this->preparedAcceptedApplication();
        $draft = Draft::factory()->create(['application_id' => $application->id]);

        $response = $this->actingAs($brand)->postJson(
            route('drafts.approve', $draft),
            ['note' => 'Approved!']
        );

        $response->assertOk()->assertJsonPath('data.status', 'approved');
        $this->assertSame($brand->id, $draft->fresh()->reviewed_by);

        Event::assertDispatched(DraftApproved::class);
    }

    public function test_brand_can_request_changes_on_submitted_draft(): void
    {
        Event::fake([DraftChangesRequested::class]);

        [$brand, $influencer, $campaign, $application] = $this->preparedAcceptedApplication();
        $draft = Draft::factory()->create(['application_id' => $application->id]);

        $response = $this->actingAs($brand)->postJson(
            route('drafts.request-changes', $draft),
            ['note' => 'Please brighten the lighting and re-shoot.']
        );

        $response->assertOk()->assertJsonPath('data.status', 'changes_requested');

        Event::assertDispatched(DraftChangesRequested::class);
    }

    public function test_request_changes_requires_note(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->preparedAcceptedApplication();
        $draft = Draft::factory()->create(['application_id' => $application->id]);

        $response = $this->actingAs($brand)->postJson(route('drafts.request-changes', $draft), []);

        $response->assertUnprocessable()->assertJsonValidationErrors(['note']);
    }

    public function test_cannot_approve_already_approved_draft(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->preparedAcceptedApplication();
        $draft = Draft::factory()->approved()->create(['application_id' => $application->id]);

        $response = $this->actingAs($brand)->postJson(route('drafts.approve', $draft));

        $response->assertUnprocessable()->assertJsonValidationErrors(['status']);
    }

    public function test_other_brand_cannot_review_draft(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->preparedAcceptedApplication();
        $stranger = User::factory()->brand()->create();
        $draft = Draft::factory()->create(['application_id' => $application->id]);

        $response = $this->actingAs($stranger)->postJson(route('drafts.approve', $draft));

        $response->assertForbidden();
    }

    public function test_influencer_can_list_drafts_for_own_application(): void
    {
        [$brand, $influencer, $campaign, $application] = $this->preparedAcceptedApplication();
        Draft::factory()->count(2)->sequence(
            ['revision_number' => 1, 'status' => DraftStatus::ChangesRequested],
            ['revision_number' => 2, 'status' => DraftStatus::Submitted],
        )->create(['application_id' => $application->id]);

        $response = $this->actingAs($influencer)->getJson(route('drafts.index', $application));

        $response->assertOk()->assertJsonCount(2, 'data');
    }
}
