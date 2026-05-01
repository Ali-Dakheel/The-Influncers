<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'brand_id' => $this->brand_id,
            'influencer_id' => $this->influencer_id,
            'amount_cents' => $this->amount_cents,
            'currency' => $this->currency,
            'status' => $this->status,
            'stripe_payment_intent_id' => $this->stripe_payment_intent_id,
            'stripe_transfer_id' => $this->stripe_transfer_id,
            'escrowed_at' => $this->escrowed_at?->toIso8601String(),
            'released_at' => $this->released_at?->toIso8601String(),
            'refunded_at' => $this->refunded_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
