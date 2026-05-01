<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'payment_id' => $this->payment_id,
            'recipient_id' => $this->recipient_id,
            'kind' => $this->kind,
            'amount_cents' => $this->amount_cents,
            'currency' => $this->currency,
            'issued_at' => $this->issued_at?->toIso8601String(),
            'paid_at' => $this->paid_at?->toIso8601String(),
            'snapshot' => $this->snapshot,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
