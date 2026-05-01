<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'title' => $this->title,
            'description' => $this->description,
            'deliverables' => $this->deliverables,
            'mood_board' => [
                'title' => $this->mood_board_title,
                'description' => $this->mood_board_description,
            ],
            'category' => $this->category,
            'country_id' => $this->country_id,
            'platforms' => $this->platforms,
            'format' => $this->format,
            'objective' => $this->objective,
            'budget_cents' => $this->budget_cents,
            'currency' => $this->currency,
            'state' => $this->state,
            'starts_on' => $this->starts_on?->toDateString(),
            'ends_on' => $this->ends_on?->toDateString(),
            'application_deadline' => $this->application_deadline?->toIso8601String(),
            'published_at' => $this->published_at?->toIso8601String(),
            'paused_at' => $this->paused_at?->toIso8601String(),
            'closed_at' => $this->closed_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
