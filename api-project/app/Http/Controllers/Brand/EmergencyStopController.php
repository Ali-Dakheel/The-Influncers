<?php

namespace App\Http\Controllers\Brand;

use App\Actions\Brand\EmergencyStopAllCampaigns;
use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\EmergencyStopRequest;
use App\Http\Resources\CampaignResource;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Group;

#[Group('Brand')]
class EmergencyStopController extends Controller
{
    public function __invoke(EmergencyStopRequest $request, EmergencyStopAllCampaigns $stop): JsonResponse
    {
        $paused = $stop($request->user(), $request->validated()['reason'] ?? null);

        return response()->json([
            'paused_count' => $paused->count(),
            'campaigns' => CampaignResource::collection($paused),
        ]);
    }
}
