<?php

namespace App\Http\Controllers\VideoPitch;

use App\Actions\VideoPitch\DecideVideoPitch;
use App\Actions\VideoPitch\SendVideoPitch;
use App\Http\Controllers\Controller;
use App\Http\Requests\VideoPitch\SendVideoPitchRequest;
use App\Http\Resources\VideoPitchResource;
use App\Models\User;
use App\Models\VideoPitch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;

#[Group('Video Pitches')]
class VideoPitchController extends Controller
{
    public function send(SendVideoPitchRequest $request, SendVideoPitch $action): JsonResponse
    {
        $brand = User::findOrFail($request->validated()['brand_id']);

        abort_unless($brand->isBrand(), 422, 'Target user is not a brand.');

        $pitch = $action($request->user(), $brand, $request->validated());

        return (new VideoPitchResource($pitch))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function mine(Request $request)
    {
        $user = $request->user();

        $query = VideoPitch::query()->latest();

        if ($user->isInfluencer() || $user->isAgency()) {
            $query->where('influencer_id', $user->id);
        } elseif ($user->isBrand()) {
            $query->where('brand_id', $user->id);
        } elseif (! $user->isAdmin()) {
            return VideoPitchResource::collection(collect());
        }

        return VideoPitchResource::collection($query->paginate());
    }

    public function show(Request $request, VideoPitch $pitch): VideoPitchResource
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
                || $pitch->influencer_id === $user->id
                || $pitch->brand_id === $user->id,
            403
        );

        return new VideoPitchResource($pitch);
    }

    public function accept(Request $request, VideoPitch $pitch, DecideVideoPitch $decide): VideoPitchResource
    {
        abort_unless($pitch->brand_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $note = $request->validate(['note' => ['nullable', 'string', 'max:2000']])['note'] ?? null;

        return new VideoPitchResource($decide->accept($pitch, $note));
    }

    public function reject(Request $request, VideoPitch $pitch, DecideVideoPitch $decide): VideoPitchResource
    {
        abort_unless($pitch->brand_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $note = $request->validate(['note' => ['nullable', 'string', 'max:2000']])['note'] ?? null;

        return new VideoPitchResource($decide->reject($pitch, $note));
    }
}
