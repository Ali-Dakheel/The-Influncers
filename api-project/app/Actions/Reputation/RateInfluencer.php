<?php

namespace App\Actions\Reputation;

use App\Enums\ApplicationStatus;
use App\Enums\CampaignState;
use App\Models\Application;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RateInfluencer
{
    /**
     * @param  array{score: int, text?: ?string}  $attributes
     */
    public function __invoke(User $brand, Application $application, array $attributes): Rating
    {
        if ($application->status !== ApplicationStatus::Accepted) {
            throw ValidationException::withMessages([
                'application' => 'Only accepted applications can be rated.',
            ]);
        }

        $campaign = $application->campaign;

        if (! in_array($campaign->state, [CampaignState::Completed, CampaignState::Closed], true)) {
            throw ValidationException::withMessages([
                'campaign' => 'Ratings can only be posted on completed or closed campaigns.',
            ]);
        }

        if ($campaign->brand_id !== $brand->id) {
            throw ValidationException::withMessages([
                'brand' => 'Only the campaign brand can post a rating.',
            ]);
        }

        return Rating::updateOrCreate(
            [
                'campaign_id' => $campaign->id,
                'brand_id' => $brand->id,
                'influencer_id' => $application->influencer_id,
            ],
            [
                'application_id' => $application->id,
                'score' => $attributes['score'],
                'text' => $attributes['text'] ?? null,
                'posted_at' => now(),
            ]
        );
    }
}
