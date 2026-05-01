<?php

namespace App\Actions\Campaign;

use App\Enums\CampaignState;
use App\Models\Campaign;
use Illuminate\Validation\ValidationException;

class PublishCampaign
{
    public function __invoke(Campaign $campaign): Campaign
    {
        if (! in_array($campaign->state, [CampaignState::Draft, CampaignState::Paused], true)) {
            throw ValidationException::withMessages([
                'state' => "Cannot publish a campaign in state '{$campaign->state->value}'.",
            ]);
        }

        $campaign->update([
            'state' => CampaignState::Published,
            'published_at' => $campaign->published_at ?? now(),
            'paused_at' => null,
        ]);

        return $campaign->fresh();
    }
}
