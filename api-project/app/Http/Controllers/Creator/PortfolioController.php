<?php

namespace App\Http\Controllers\Creator;

use App\Actions\Creator\UpdatePortfolio;
use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\UpdatePortfolioRequest;
use App\Http\Resources\PortfolioResource;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;

#[Group('Creator OS')]
class PortfolioController extends Controller
{
    public function showMine(Request $request): JsonResponse
    {
        $portfolio = Portfolio::firstOrCreate(['user_id' => $request->user()->id]);

        return (new PortfolioResource($portfolio))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function showForUser(User $user): JsonResponse
    {
        abort_unless($user->isInfluencer() || $user->isAgency(), 404);

        $portfolio = Portfolio::firstOrCreate(['user_id' => $user->id]);

        return (new PortfolioResource($portfolio))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update(UpdatePortfolioRequest $request, UpdatePortfolio $action): PortfolioResource
    {
        return new PortfolioResource(
            $action($request->user(), $request->validated())
        );
    }
}
