<?php

namespace App\Http\Controllers\Campaign;

use App\Actions\Campaign\CreateCampaign;
use App\Actions\Campaign\UpdateCampaign;
use App\Enums\CampaignState;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CreateCampaignRequest;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;

#[Group('Campaigns')]
class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Campaign::query();

        if ($user->isBrand()) {
            $query->where('brand_id', $user->id);
        } elseif (! $user->isAdmin()) {
            // influencer/agency: only see published, paused, or closed campaigns
            $query->whereIn('state', [
                CampaignState::Published,
                CampaignState::Paused,
                CampaignState::Closed,
            ]);
        }

        return CampaignResource::collection($query->latest()->paginate());
    }

    public function show(Campaign $campaign): CampaignResource
    {
        $this->authorize('view', $campaign);

        return new CampaignResource($campaign);
    }

    public function store(CreateCampaignRequest $request, CreateCampaign $createCampaign): JsonResponse
    {
        $campaign = $createCampaign($request->user(), $request->validated());

        return (new CampaignResource($campaign))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign, UpdateCampaign $updateCampaign): CampaignResource
    {
        $campaign = $updateCampaign($campaign, $request->validated());

        return new CampaignResource($campaign);
    }

    public function destroy(Campaign $campaign): JsonResponse
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
