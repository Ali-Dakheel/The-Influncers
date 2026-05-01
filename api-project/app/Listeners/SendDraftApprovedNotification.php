<?php

namespace App\Listeners;

use App\Events\DraftApproved;
use App\Notifications\DraftApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDraftApprovedNotification implements ShouldQueue
{
    public function handle(DraftApproved $event): void
    {
        $event->draft->application->influencer->notify(
            new DraftApprovedNotification($event->draft)
        );
    }
}
