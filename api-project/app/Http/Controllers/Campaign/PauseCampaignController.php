<?php

namespace App\Http\Controllers\Campaign;

use App\Actions\Campaign\PauseCampaign;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use Knuckles\Scribe\Attributes\Group;

#[Group('Campaigns')]
class PauseCampaignController extends Controller
{
    public function __invoke(Campaign $campaign, PauseCampaign $pauseCampaign): CampaignResource
    {
        $this->authorize('transitionState', $campaign);

        return new CampaignResource($pauseCampaign($campaign));
    }
}
