<?php

namespace App\Http\Controllers\Outcome;

use App\Actions\Campaign\RecordOutcomeMetrics;
use App\Http\Controllers\Controller;
use App\Http\Requests\Outcome\RecordOutcomeRequest;
use App\Http\Resources\OutcomeResource;
use App\Models\Campaign;
use App\Models\Outcome;
use Knuckles\Scribe\Attributes\Group;

#[Group('Outcomes')]
class OutcomeController extends Controller
{
    public function indexForCampaign(Campaign $campaign)
    {
        $this->authorize('viewApplications', $campaign);

        return OutcomeResource::collection(
            $campaign->outcomes()->latest('recorded_at')->paginate()
        );
    }

    public function show(Outcome $outcome): OutcomeResource
    {
        $user = request()->user();

        abort_unless(
            $user->isAdmin()
                || $outcome->influencer_id === $user->id
                || $outcome->campaign->brand_id === $user->id,
            403
        );

        return new OutcomeResource($outcome);
    }

    public function record(RecordOutcomeRequest $request, Outcome $outcome, RecordOutcomeMetrics $action): OutcomeResource
    {
        return new OutcomeResource($action($outcome, $request->validated()));
    }
}
