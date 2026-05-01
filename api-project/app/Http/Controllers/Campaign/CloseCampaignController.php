<?php

namespace App\Http\Controllers\Campaign;

use App\Actions\Campaign\CloseCampaign;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use Knuckles\Scribe\Attributes\Group;

#[Group('Campaigns')]
class CloseCampaignController extends Controller
{
    public function __invoke(Campaign $campaign, CloseCampaign $closeCampaign): CampaignResource
    {
        $this->authorize('transitionState', $campaign);

        return new CampaignResource($closeCampaign($campaign));
    }
}
