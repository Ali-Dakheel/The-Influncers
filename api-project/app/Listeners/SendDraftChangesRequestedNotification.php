<?php

namespace App\Listeners;

use App\Events\DraftChangesRequested;
use App\Notifications\DraftChangesRequestedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDraftChangesRequestedNotification implements ShouldQueue
{
    public function handle(DraftChangesRequested $event): void
    {
        $event->draft->application->influencer->notify(
            new DraftChangesRequestedNotification($event->draft)
        );
    }
}
