<?php

namespace App\Actions\Campaign;

use App\Models\Campaign;

class UpdateCampaign
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(Campaign $campaign, array $attributes): Campaign
    {
        $campaign->update($attributes);

        return $campaign->fresh();
    }
}
