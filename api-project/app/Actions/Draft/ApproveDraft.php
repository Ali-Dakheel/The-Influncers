<?php

namespace App\Actions\Draft;

use App\Enums\DraftStatus;
use App\Events\DraftApproved;
use App\Models\Draft;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ApproveDraft
{
    public function __invoke(Draft $draft, User $reviewer, ?string $note = null): Draft
    {
        if ($draft->status !== DraftStatus::Submitted) {
            throw ValidationException::withMessages([
                'status' => "Cannot approve a draft in state '{$draft->status->value}'.",
            ]);
        }

        $draft->update([
            'status' => DraftStatus::Approved,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
            'review_note' => $note,
        ]);

        $draft = $draft->fresh();

        event(new DraftApproved($draft));

        return $draft;
    }
}
