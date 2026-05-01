<?php

namespace App\Actions\Campaign;

use App\Enums\CampaignState;
use App\Models\Campaign;
use App\Models\User;

class CreateCampaign
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(User $brand, array $attributes): Campaign
    {
        return Campaign::create([
            ...$attributes,
            'brand_id' => $brand->id,
            'state' => CampaignState::Draft,
        ]);
    }
}
