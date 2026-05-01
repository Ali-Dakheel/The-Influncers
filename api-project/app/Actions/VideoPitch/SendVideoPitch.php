<?php

namespace App\Actions\VideoPitch;

use App\Enums\VideoPitchStatus;
use App\Models\User;
use App\Models\VideoPitch;

class SendVideoPitch
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(User $influencer, User $brand, array $attributes): VideoPitch
    {
        return VideoPitch::create([
            'influencer_id' => $influencer->id,
            'brand_id' => $brand->id,
            'campaign_id' => $attributes['campaign_id'] ?? null,
            'video_url' => $attributes['video_url'],
            'message' => $attributes['message'] ?? null,
            'status' => VideoPitchStatus::Pending,
        ]);
    }
}
