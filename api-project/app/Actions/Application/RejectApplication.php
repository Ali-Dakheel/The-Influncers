<?php

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RejectApplication
{
    public function __invoke(Application $application, User $decidedBy, ?string $note = null): Application
    {
        if ($application->status !== ApplicationStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => "Cannot reject an application in state '{$application->status->value}'.",
            ]);
        }

        $application->update([
            'status' => ApplicationStatus::Rejected,
            'decided_at' => now(),
            'decided_by' => $decidedBy->id,
            'decision_note' => $note,
        ]);

        return $application->fresh();
    }
}
