<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Applications')]
class ApplicationController extends Controller
{
    /**
     * List applications for a given campaign (brand-only view).
     */
    public function indexForCampaign(Request $request, Campaign $campaign)
    {
        $this->authorize('viewApplications', $campaign);

        return ApplicationResource::collection(
            $campaign->applications()->latest('applied_at')->paginate()
        );
    }

    /**
     * List the current user's own applications (influencer view).
     */
    public function mine(Request $request)
    {
        return ApplicationResource::collection(
            Application::query()
                ->where('influencer_id', $request->user()->id)
                ->latest('applied_at')
                ->paginate()
        );
    }

    public function show(Application $application): ApplicationResource
    {
        $this->authorize('view', $application);

        return new ApplicationResource($application);
    }
}
