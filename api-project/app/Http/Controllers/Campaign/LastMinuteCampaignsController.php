<?php

namespace App\Http\Controllers\Campaign;

use App\Enums\CampaignState;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Campaigns')]
class LastMinuteCampaignsController extends Controller
{
    public function __invoke(Request $request)
    {
        $now = now();

        $campaigns = Campaign::query()
            ->where('is_urgent', true)
            ->where('state', CampaignState::Published)
            ->whereNull('archived_at')
            ->where(function ($q) use ($now) {
                $q->whereNull('urgent_expires_at')->orWhere('urgent_expires_at', '>', $now);
            })
            ->latest('urgent_expires_at')
            ->paginate();

        return CampaignResource::collection($campaigns);
    }
}
