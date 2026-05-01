<?php

namespace App\Actions\Draft;

use App\Enums\ApplicationStatus;
use App\Enums\DraftStatus;
use App\Events\DraftSubmitted;
use App\Models\Application;
use App\Models\Draft;
use Illuminate\Validation\ValidationException;

class SubmitDraft
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __invoke(Application $application, array $attributes): Draft
    {
        if ($application->status !== ApplicationStatus::Accepted) {
            throw ValidationException::withMessages([
                'application' => 'Drafts can only be submitted for accepted applications.',
            ]);
        }

        $latest = $application->drafts()->latest('revision_number')->first();

        if ($latest && $latest->status === DraftStatus::Approved) {
            throw ValidationException::withMessages([
                'draft' => 'A draft has already been approved for this application.',
            ]);
        }

        if ($latest && $latest->status === DraftStatus::Submitted) {
            throw ValidationException::withMessages([
                'draft' => 'There is already a pending draft awaiting review.',
            ]);
        }

        $nextRevision = ($latest?->revision_number ?? 0) + 1;

        $draft = Draft::create([
            'application_id' => $application->id,
            'revision_number' => $nextRevision,
            'platform' => $attributes['platform'],
            'file_path' => $attributes['file_path'] ?? null,
            'file_url' => $attributes['file_url'] ?? null,
            'caption' => $attributes['caption'] ?? null,
            'status' => DraftStatus::Submitted,
            'submitted_at' => now(),
        ]);

        event(new DraftSubmitted($draft));

        return $draft;
    }
}
