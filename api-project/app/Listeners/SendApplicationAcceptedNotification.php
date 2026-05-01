<?php

namespace App\Listeners;

use App\Events\ApplicationAccepted;
use App\Notifications\ApplicationAcceptedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendApplicationAcceptedNotification implements ShouldQueue
{
    public function handle(ApplicationAccepted $event): void
    {
        $event->application->influencer->notify(
            new ApplicationAcceptedNotification($event->application)
        );
    }
}
