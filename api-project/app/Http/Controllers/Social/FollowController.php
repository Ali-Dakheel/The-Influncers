<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Http\Resources\FollowResource;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;

#[Group('Social')]
class FollowController extends Controller
{
    public function follow(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            abort(422, 'You cannot follow yourself.');
        }

        $follow = Follow::firstOrCreate([
            'follower_id' => $request->user()->id,
            'followed_id' => $user->id,
        ]);

        return (new FollowResource($follow))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function unfollow(Request $request, User $user): JsonResponse
    {
        Follow::where('follower_id', $request->user()->id)
            ->where('followed_id', $user->id)
            ->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function following(Request $request): JsonResponse
    {
        $followedIds = Follow::where('follower_id', $request->user()->id)->pluck('followed_id');

        return response()->json([
            'data' => User::whereIn('id', $followedIds)
                ->select('id', 'name', 'role')
                ->get(),
        ]);
    }

    public function followers(Request $request): JsonResponse
    {
        $followerIds = Follow::where('followed_id', $request->user()->id)->pluck('follower_id');

        return response()->json([
            'data' => User::whereIn('id', $followerIds)
                ->select('id', 'name', 'role')
                ->get(),
        ]);
    }
}
