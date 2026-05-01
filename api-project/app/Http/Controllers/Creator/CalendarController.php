<?php

namespace App\Http\Controllers\Creator;

use App\Enums\ApplicationStatus;
use App\Enums\CampaignState;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Creator OS')]
class CalendarController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Campaign::query()->with('country');

        if ($user->isBrand()) {
            $query->where('brand_id', $user->id);
        } elseif ($user->isInfluencer() || $user->isAgency()) {
            $query->whereHas('applications', function ($q) use ($user) {
                $q->where('influencer_id', $user->id)
                    ->where('status', ApplicationStatus::Accepted);
            });
        } elseif (! $user->isAdmin()) {
            return response()->json(['data' => []]);
        }

        $campaigns = $query
            ->whereIn('state', [CampaignState::Published, CampaignState::Paused, CampaignState::Completed])
            ->orderBy('starts_on')
            ->get();

        $events = $campaigns->flatMap(function (Campaign $campaign) {
            $items = [];

            if ($campaign->starts_on) {
                $items[] = [
                    'campaign_id' => $campaign->id,
                    'campaign_title' => $campaign->title,
                    'type' => 'starts_on',
                    'date' => $campaign->starts_on->toDateString(),
                ];
            }
            if ($campaign->ends_on) {
                $items[] = [
                    'campaign_id' => $campaign->id,
                    'campaign_title' => $campaign->title,
                    'type' => 'ends_on',
                    'date' => $campaign->ends_on->toDateString(),
                ];
            }
            if ($campaign->application_deadline) {
                $items[] = [
                    'campaign_id' => $campaign->id,
                    'campaign_title' => $campaign->title,
                    'type' => 'application_deadline',
                    'date' => $campaign->application_deadline->toIso8601String(),
                ];
            }

            return $items;
        })->values();

        return response()->json(['data' => $events]);
    }
}
