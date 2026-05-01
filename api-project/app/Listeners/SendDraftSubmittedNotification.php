<?php

namespace App\Listeners;

use App\Events\DraftSubmitted;
use App\Notifications\DraftSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDraftSubmittedNotification implements ShouldQueue
{
    public function handle(DraftSubmitted $event): void
    {
        $event->draft->application->campaign->brand->notify(
            new DraftSubmittedNotification($event->draft)
        );
    }
}
