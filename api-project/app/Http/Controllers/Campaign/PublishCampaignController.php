<?php

namespace App\Http\Controllers\Campaign;

use App\Actions\Campaign\PublishCampaign;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use Knuckles\Scribe\Attributes\Group;

#[Group('Campaigns')]
class PublishCampaignController extends Controller
{
    public function __invoke(Campaign $campaign, PublishCampaign $publishCampaign): CampaignResource
    {
        $this->authorize('transitionState', $campaign);

        return new CampaignResource($publishCampaign($campaign));
    }
}
