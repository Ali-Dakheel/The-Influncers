<?php

namespace App\Http\Controllers\Draft;

use App\Http\Controllers\Controller;
use App\Http\Resources\DraftResource;
use App\Models\Application;
use App\Models\Draft;
use Knuckles\Scribe\Attributes\Group;

#[Group('Drafts')]
class DraftController extends Controller
{
    public function indexForApplication(Application $application)
    {
        $this->authorize('view', $application);

        return DraftResource::collection(
            $application->drafts()->orderByDesc('revision_number')->get()
        );
    }

    public function show(Draft $draft): DraftResource
    {
        $this->authorize('view', $draft);

        return new DraftResource($draft);
    }
}
