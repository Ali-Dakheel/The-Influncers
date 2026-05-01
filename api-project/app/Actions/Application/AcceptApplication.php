<?php

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Events\ApplicationAccepted;
use App\Models\Application;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AcceptApplication
{
    public function __invoke(Application $application, User $decidedBy, ?string $note = null): Application
    {
        if ($application->status !== ApplicationStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => "Cannot accept an application in state '{$application->status->value}'.",
            ]);
        }

        $application->update([
            'status' => ApplicationStatus::Accepted,
            'decided_at' => now(),
            'decided_by' => $decidedBy->id,
            'decision_note' => $note,
        ]);

        $application = $application->fresh();

        event(new ApplicationAccepted($application));

        return $application;
    }
}
