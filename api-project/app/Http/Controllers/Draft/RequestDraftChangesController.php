<?php

namespace App\Http\Controllers\Draft;

use App\Actions\Draft\RequestDraftChanges;
use App\Http\Controllers\Controller;
use App\Http\Requests\Draft\RequestChangesRequest;
use App\Http\Resources\DraftResource;
use App\Models\Draft;
use Knuckles\Scribe\Attributes\Group;

#[Group('Drafts')]
class RequestDraftChangesController extends Controller
{
    public function __invoke(RequestChangesRequest $request, Draft $draft, RequestDraftChanges $requestChanges): DraftResource
    {
        return new DraftResource(
            $requestChanges($draft, $request->user(), $request->validated()['note'])
        );
    }
}
