<?php

namespace App\Http\Controllers\Draft;

use App\Actions\Draft\ApproveDraft;
use App\Http\Controllers\Controller;
use App\Http\Requests\Draft\ReviewDraftRequest;
use App\Http\Resources\DraftResource;
use App\Models\Draft;
use Knuckles\Scribe\Attributes\Group;

#[Group('Drafts')]
class ApproveDraftController extends Controller
{
    public function __invoke(ReviewDraftRequest $request, Draft $draft, ApproveDraft $approve): DraftResource
    {
        return new DraftResource(
            $approve($draft, $request->user(), $request->validated()['note'] ?? null)
        );
    }
}
