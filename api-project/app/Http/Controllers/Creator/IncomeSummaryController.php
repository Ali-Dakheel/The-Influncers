<?php

namespace App\Http\Controllers\Creator;

use App\Enums\ApplicationStatus;
use App\Enums\CampaignState;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Outcome;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Creator OS')]
class IncomeSummaryController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $accepted = Application::query()
            ->where('influencer_id', $user->id)
            ->where('status', ApplicationStatus::Accepted)
            ->with('campaign')
            ->get();

        $earnedFromCompleted = $accepted
            ->filter(fn ($app) => $app->campaign?->state === CampaignState::Completed)
            ->sum('proposed_price_cents');

        $pendingCompletion = $accepted
            ->filter(fn ($app) => $app->campaign && in_array($app->campaign->state, [CampaignState::Published, CampaignState::Paused], true))
            ->sum('proposed_price_cents');

        $outcomes = Outcome::where('influencer_id', $user->id)->get();
        $totalReach = $outcomes->sum('reach');
        $totalEngagement = $outcomes->sum('engagement');
        $totalConversions = $outcomes->sum('conversions');

        return response()->json([
            'data' => [
                'earned_cents' => (int) $earnedFromCompleted,
                'pending_cents' => (int) $pendingCompletion,
                'completed_campaigns' => $accepted
                    ->filter(fn ($app) => $app->campaign?->state === CampaignState::Completed)
                    ->count(),
                'active_campaigns' => $accepted
                    ->filter(fn ($app) => $app->campaign && in_array($app->campaign->state, [CampaignState::Published, CampaignState::Paused], true))
                    ->count(),
                'lifetime_reach' => (int) $totalReach,
                'lifetime_engagement' => (int) $totalEngagement,
                'lifetime_conversions' => (int) $totalConversions,
            ],
        ]);
    }
}
