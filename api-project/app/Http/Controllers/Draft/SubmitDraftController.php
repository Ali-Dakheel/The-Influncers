<?php

namespace App\Http\Controllers\Draft;

use App\Actions\Draft\SubmitDraft;
use App\Http\Controllers\Controller;
use App\Http\Requests\Draft\SubmitDraftRequest;
use App\Http\Resources\DraftResource;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;

#[Group('Drafts')]
class SubmitDraftController extends Controller
{
    public function __invoke(SubmitDraftRequest $request, Application $application, SubmitDraft $submit): JsonResponse
    {
        $draft = $submit($application, $request->validated());

        return (new DraftResource($draft))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
