<?php

namespace App\Actions\Campaign;

use App\Models\Outcome;

class RecordOutcomeMetrics
{
    /**
     * Record the actual results once the influencer's post is live.
     * Called by the influencer (or admin) post-completion.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(Outcome $outcome, array $attributes): Outcome
    {
        $outcome->update([
            'final_post_url' => $attributes['final_post_url'] ?? $outcome->final_post_url,
            'reach' => $attributes['reach'] ?? $outcome->reach,
            'engagement' => $attributes['engagement'] ?? $outcome->engagement,
            'conversions' => $attributes['conversions'] ?? $outcome->conversions,
            'cost_per_result_cents' => $this->computeCpr($attributes, $outcome),
            'recorded_at' => now(),
        ]);

        return $outcome->fresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function computeCpr(array $attributes, Outcome $outcome): ?int
    {
        $paid = $outcome->paid_price_cents;
        $conversions = $attributes['conversions'] ?? $outcome->conversions;

        if ($paid === null || $conversions === null || $conversions === 0) {
            return $attributes['cost_per_result_cents'] ?? $outcome->cost_per_result_cents;
        }

        return (int) round($paid / $conversions);
    }
}
