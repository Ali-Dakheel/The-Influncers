<?php

namespace App\Http\Controllers\Application;

use App\Actions\Application\RejectApplication;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\DecideApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Knuckles\Scribe\Attributes\Group;

#[Group('Applications')]
class RejectApplicationController extends Controller
{
    public function __invoke(DecideApplicationRequest $request, Application $application, RejectApplication $reject): ApplicationResource
    {
        return new ApplicationResource(
            $reject($application, $request->user(), $request->validated()['note'] ?? null)
        );
    }
}
