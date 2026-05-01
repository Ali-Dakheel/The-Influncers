<?php

namespace App\Http\Controllers\Campaign;

use App\Actions\Campaign\CompleteCampaign;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use Knuckles\Scribe\Attributes\Group;

#[Group('Campaigns')]
class CompleteCampaignController extends Controller
{
    public function __invoke(Campaign $campaign, CompleteCampaign $complete): CampaignResource
    {
        $this->authorize('transitionState', $campaign);

        return new CampaignResource($complete($campaign));
    }
}
