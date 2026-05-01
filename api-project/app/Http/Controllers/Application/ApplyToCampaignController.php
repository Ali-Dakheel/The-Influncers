<?php

namespace App\Http\Controllers\Application;

use App\Actions\Application\ApplyToCampaign;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\ApplyToCampaignRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;

#[Group('Applications')]
class ApplyToCampaignController extends Controller
{
    public function __invoke(ApplyToCampaignRequest $request, Campaign $campaign, ApplyToCampaign $apply): JsonResponse
    {
        $application = $apply($request->user(), $campaign, $request->validated());

        return (new ApplicationResource($application))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
