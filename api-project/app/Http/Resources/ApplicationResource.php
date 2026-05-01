<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'influencer_id' => $this->influencer_id,
            'status' => $this->status,
            'pitch' => $this->pitch,
            'proposed_price_cents' => $this->proposed_price_cents,
            'currency' => $this->currency,
            'applied_at' => $this->applied_at?->toIso8601String(),
            'decided_at' => $this->decided_at?->toIso8601String(),
            'decided_by' => $this->decided_by,
            'decision_note' => $this->decision_note,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
