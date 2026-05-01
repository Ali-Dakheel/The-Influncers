<?php

namespace App\Http\Controllers\Creator;

use App\Actions\Creator\CreatePricePackage;
use App\Actions\Creator\SetPriceListItem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\CreatePricePackageRequest;
use App\Http\Requests\Creator\SetPriceListItemRequest;
use App\Http\Resources\PriceListItemResource;
use App\Http\Resources\PricePackageResource;
use App\Models\PriceListItem;
use App\Models\PricePackage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;

#[Group('Creator OS')]
class PriceListController extends Controller
{
    public function showMine(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'items' => PriceListItemResource::collection($user->priceListItems),
            'packages' => PricePackageResource::collection($user->pricePackages),
        ]);
    }

    public function showForUser(User $user): JsonResponse
    {
        abort_unless($user->isInfluencer() || $user->isAgency(), 404);

        return response()->json([
            'items' => PriceListItemResource::collection($user->priceListItems),
            'packages' => PricePackageResource::collection($user->pricePackages),
        ]);
    }

    public function setItem(SetPriceListItemRequest $request, SetPriceListItem $action): JsonResponse
    {
        $item = $action($request->user(), $request->validated());

        return (new PriceListItemResource($item))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function deleteItem(Request $request, PriceListItem $item): JsonResponse
    {
        abort_unless($item->user_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $item->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function createPackage(CreatePricePackageRequest $request, CreatePricePackage $action): JsonResponse
    {
        $package = $action($request->user(), $request->validated());

        return (new PricePackageResource($package))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function deletePackage(Request $request, PricePackage $package): JsonResponse
    {
        abort_unless($package->user_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $package->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
