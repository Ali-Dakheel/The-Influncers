<?php

namespace App\Actions\Campaign;

use App\Enums\ApplicationStatus;
use App\Enums\CampaignState;
use App\Enums\DraftStatus;
use App\Events\CampaignCompleted;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Outcome;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompleteCampaign
{
    public function __invoke(Campaign $campaign): Campaign
    {
        if (in_array($campaign->state, [CampaignState::Closed, CampaignState::Completed], true)) {
            throw ValidationException::withMessages([
                'state' => "Campaign is already terminal (state: '{$campaign->state->value}').",
            ]);
        }

        DB::transaction(function () use ($campaign) {
            $campaign->update([
                'state' => CampaignState::Completed,
                'completed_at' => now(),
            ]);

            // Write an OutcomeRecord per accepted application with an approved draft.
            // The Performance Memory (Phase 2A) will read from this table.
            $accepted = $campaign->applications()
                ->where('status', ApplicationStatus::Accepted)
                ->with(['drafts' => fn ($q) => $q->where('status', DraftStatus::Approved)->latest('revision_number')->limit(1)])
                ->get();

            foreach ($accepted as $application) {
                /** @var Application $application */
                $approvedDraft = $application->drafts->first();

                if (! $approvedDraft) {
                    continue;
                }

                Outcome::updateOrCreate(
                    [
                        'campaign_id' => $campaign->id,
                        'application_id' => $application->id,
                    ],
                    [
                        'influencer_id' => $application->influencer_id,
                        'platform' => $approvedDraft->platform,
                        'category' => $campaign->category,
                        'country_id' => $campaign->country_id,
                        'format' => $campaign->format,
                        'objective' => $campaign->objective,
                        'paid_price_cents' => $application->proposed_price_cents,
                    ]
                );
            }
        });

        $campaign = $campaign->fresh();

        event(new CampaignCompleted($campaign));

        return $campaign;
    }
}
