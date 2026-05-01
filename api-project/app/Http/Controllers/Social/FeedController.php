<?php

namespace App\Http\Controllers\Social;

use App\Enums\CampaignState;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Follow;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Social')]
class FeedController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $followedIds = Follow::where('follower_id', $request->user()->id)->pluck('followed_id');

        if ($followedIds->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $events = collect();

        // Campaigns published or completed by followed brands
        Campaign::query()
            ->whereIn('brand_id', $followedIds)
            ->whereIn('state', [CampaignState::Published, CampaignState::Completed])
            ->latest('updated_at')
            ->limit(30)
            ->get()
            ->each(function ($campaign) use ($events) {
                $events->push([
                    'type' => 'campaign.'.$campaign->state->value,
                    'actor_id' => $campaign->brand_id,
                    'campaign_id' => $campaign->id,
                    'campaign_title' => $campaign->title,
                    'occurred_at' => $campaign->updated_at?->toIso8601String(),
                ]);
            });

        // Ratings posted by followed brands or about followed influencers
        Rating::query()
            ->where(function ($q) use ($followedIds) {
                $q->whereIn('brand_id', $followedIds)->orWhereIn('influencer_id', $followedIds);
            })
            ->latest('posted_at')
            ->limit(30)
            ->get()
            ->each(function ($rating) use ($events) {
                $events->push([
                    'type' => 'rating.posted',
                    'actor_id' => $rating->brand_id,
                    'subject_id' => $rating->influencer_id,
                    'campaign_id' => $rating->campaign_id,
                    'score' => $rating->score,
                    'occurred_at' => $rating->posted_at?->toIso8601String(),
                ]);
            });

        $sorted = $events->sortByDesc('occurred_at')->values()->take(50);

        return response()->json(['data' => $sorted]);
    }
}
