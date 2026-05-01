<?php

namespace App\Actions\VideoPitch;

use App\Enums\VideoPitchStatus;
use App\Models\VideoPitch;
use Illuminate\Validation\ValidationException;

class DecideVideoPitch
{
    public function accept(VideoPitch $pitch, ?string $note = null): VideoPitch
    {
        return $this->decide($pitch, VideoPitchStatus::Accepted, $note);
    }

    public function reject(VideoPitch $pitch, ?string $note = null): VideoPitch
    {
        return $this->decide($pitch, VideoPitchStatus::Rejected, $note);
    }

    private function decide(VideoPitch $pitch, VideoPitchStatus $status, ?string $note): VideoPitch
    {
        if ($pitch->status !== VideoPitchStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => "Cannot decide on a pitch in state '{$pitch->status->value}'.",
            ]);
        }

        $pitch->update([
            'status' => $status,
            'reviewed_at' => now(),
            'decision_note' => $note,
        ]);

        return $pitch->fresh();
    }
}
