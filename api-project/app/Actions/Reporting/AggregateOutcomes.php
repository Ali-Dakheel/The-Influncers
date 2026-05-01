<?php

namespace App\Actions\Reporting;

use App\Models\Outcome;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AggregateOutcomes
{
    /**
     * Build an outcomes report scoped + filtered for the requesting user.
     *
     * Visibility:
     *  - admin (country_id null) sees everything
     *  - admin (country_id set) sees only outcomes in their country
     *  - brand sees only their own campaigns' outcomes
     *  - influencer sees only their own outcomes
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function __invoke(User $user, array $filters): array
    {
        $query = Outcome::query();

        $this->applyVisibility($query, $user);
        $this->applyFilters($query, $filters);

        $totals = (clone $query)->selectRaw(
            'COUNT(*) as count, '.
            'COALESCE(SUM(reach), 0) as total_reach, '.
            'COALESCE(SUM(engagement), 0) as total_engagement, '.
            'COALESCE(SUM(conversions), 0) as total_conversions, '.
            'COALESCE(SUM(paid_price_cents), 0) as total_paid_cents, '.
            'AVG(cost_per_result_cents) as avg_cpr_cents'
        )->first();

        $byPlatform = (clone $query)
            ->selectRaw('platform, COUNT(*) as count, COALESCE(SUM(reach), 0) as reach, COALESCE(SUM(engagement), 0) as engagement, COALESCE(SUM(conversions), 0) as conversions, COALESCE(SUM(paid_price_cents), 0) as paid_cents')
            ->groupBy('platform')
            ->get();

        $byCountry = (clone $query)
            ->selectRaw('country_id, COUNT(*) as count, COALESCE(SUM(reach), 0) as reach, COALESCE(SUM(engagement), 0) as engagement, COALESCE(SUM(conversions), 0) as conversions, COALESCE(SUM(paid_price_cents), 0) as paid_cents')
            ->groupBy('country_id')
            ->get();

        $byCategory = (clone $query)
            ->selectRaw('category, COUNT(*) as count, COALESCE(SUM(reach), 0) as reach, COALESCE(SUM(engagement), 0) as engagement, COALESCE(SUM(conversions), 0) as conversions, COALESCE(SUM(paid_price_cents), 0) as paid_cents')
            ->groupBy('category')
            ->get();

        return [
            'filters_applied' => $filters,
            'totals' => [
                'campaign_count' => (int) $totals->count,
                'reach' => (int) $totals->total_reach,
                'engagement' => (int) $totals->total_engagement,
                'conversions' => (int) $totals->total_conversions,
                'paid_cents' => (int) $totals->total_paid_cents,
                'average_cpr_cents' => $totals->avg_cpr_cents !== null ? (int) round($totals->avg_cpr_cents) : null,
            ],
            'by_platform' => $byPlatform,
            'by_country' => $byCountry,
            'by_category' => $byCategory,
        ];
    }

    private function applyVisibility(Builder $query, User $user): void
    {
        if ($user->isAdmin()) {
            if ($user->country_id !== null) {
                $query->where('country_id', $user->country_id);
            }

            return;
        }

        if ($user->isBrand()) {
            $query->whereHas('campaign', fn (Builder $c) => $c->where('brand_id', $user->id));

            return;
        }

        if ($user->isInfluencer() || $user->isAgency()) {
            $query->where('influencer_id', $user->id);

            return;
        }

        // Unknown role: show nothing
        $query->whereRaw('1 = 0');
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['brand_id'])) {
            $query->whereHas('campaign', fn (Builder $c) => $c->where('brand_id', $filters['brand_id']));
        }
        if (! empty($filters['influencer_id'])) {
            $query->where('influencer_id', $filters['influencer_id']);
        }
        if (! empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }
        if (! empty($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }
        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (! empty($filters['from'])) {
            $query->where('created_at', '>=', $filters['from']);
        }
        if (! empty($filters['to'])) {
            $query->where('created_at', '<=', $filters['to']);
        }
    }
}
