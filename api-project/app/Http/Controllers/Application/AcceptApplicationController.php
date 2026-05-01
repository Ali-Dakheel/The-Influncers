<?php

namespace App\Http\Controllers\Application;

use App\Actions\Application\AcceptApplication;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\DecideApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Knuckles\Scribe\Attributes\Group;

#[Group('Applications')]
class AcceptApplicationController extends Controller
{
    public function __invoke(DecideApplicationRequest $request, Application $application, AcceptApplication $accept): ApplicationResource
    {
        return new ApplicationResource(
            $accept($application, $request->user(), $request->validated()['note'] ?? null)
        );
    }
}
