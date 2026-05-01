<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutcomeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'application_id' => $this->application_id,
            'influencer_id' => $this->influencer_id,
            'platform' => $this->platform,
            'category' => $this->category,
            'country_id' => $this->country_id,
            'format' => $this->format,
            'objective' => $this->objective,
            'final_post_url' => $this->final_post_url,
            'reach' => $this->reach,
            'engagement' => $this->engagement,
            'conversions' => $this->conversions,
            'cost_per_result_cents' => $this->cost_per_result_cents,
            'paid_price_cents' => $this->paid_price_cents,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
        ];
    }
}
