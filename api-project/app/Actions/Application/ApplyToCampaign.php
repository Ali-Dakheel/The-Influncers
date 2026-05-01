<?php

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\User;

class ApplyToCampaign
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(User $influencer, Campaign $campaign, array $attributes): Application
    {
        return Application::create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
            'status' => ApplicationStatus::Pending,
            'pitch' => $attributes['pitch'],
            'proposed_price_cents' => $attributes['proposed_price_cents'] ?? null,
            'currency' => $attributes['currency'] ?? $campaign->currency,
            'applied_at' => now(),
        ]);
    }
}
