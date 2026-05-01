<?php

namespace App\Http\Controllers\Reputation;

use App\Actions\Reputation\RateInfluencer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reputation\RateInfluencerRequest;
use App\Http\Resources\RatingResource;
use App\Models\Application;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;

#[Group('Reputation')]
class RatingController extends Controller
{
    public function rateApplication(RateInfluencerRequest $request, Application $application, RateInfluencer $action): JsonResponse
    {
        $rating = $action($request->user(), $application, $request->validated());

        return (new RatingResource($rating))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function indexForInfluencer(User $influencer)
    {
        $ratings = Rating::query()
            ->where('influencer_id', $influencer->id)
            ->latest('posted_at')
            ->paginate();

        return RatingResource::collection($ratings);
    }
}
