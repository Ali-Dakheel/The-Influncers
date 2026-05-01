<?php

namespace App\Actions\Draft;

use App\Enums\DraftStatus;
use App\Events\DraftChangesRequested;
use App\Models\Draft;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RequestDraftChanges
{
    public function __invoke(Draft $draft, User $reviewer, string $note): Draft
    {
        if ($draft->status !== DraftStatus::Submitted) {
            throw ValidationException::withMessages([
                'status' => "Cannot request changes for a draft in state '{$draft->status->value}'.",
            ]);
        }

        $draft->update([
            'status' => DraftStatus::ChangesRequested,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
            'review_note' => $note,
        ]);

        $draft = $draft->fresh();

        event(new DraftChangesRequested($draft));

        return $draft;
    }
}
