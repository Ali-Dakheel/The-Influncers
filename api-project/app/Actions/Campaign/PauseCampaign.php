<?php

namespace App\Actions\Campaign;

use App\Enums\CampaignState;
use App\Models\Campaign;
use Illuminate\Validation\ValidationException;

class PauseCampaign
{
    public function __invoke(Campaign $campaign): Campaign
    {
        if ($campaign->state !== CampaignState::Published) {
            throw ValidationException::withMessages([
                'state' => "Only published campaigns can be paused (current: '{$campaign->state->value}').",
            ]);
        }

        $campaign->update([
            'state' => CampaignState::Paused,
            'paused_at' => now(),
        ]);

        return $campaign->fresh();
    }
}
