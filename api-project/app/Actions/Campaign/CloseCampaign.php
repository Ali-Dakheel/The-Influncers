<?php

namespace App\Actions\Campaign;

use App\Enums\CampaignState;
use App\Models\Campaign;
use Illuminate\Validation\ValidationException;

class CloseCampaign
{
    public function __invoke(Campaign $campaign): Campaign
    {
        if ($campaign->state === CampaignState::Closed || $campaign->state === CampaignState::Completed) {
            throw ValidationException::withMessages([
                'state' => "Campaign is already terminal (state: '{$campaign->state->value}').",
            ]);
        }

        $campaign->update([
            'state' => CampaignState::Closed,
            'closed_at' => now(),
        ]);

        return $campaign->fresh();
    }
}
