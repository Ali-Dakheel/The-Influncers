<?php

namespace App\Notifications;

use App\Models\Draft;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DraftSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Draft $draft) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $campaign = $this->draft->application->campaign;

        return (new MailMessage)
            ->subject("New draft for review — '{$campaign->title}'")
            ->line("A new draft (revision #{$this->draft->revision_number}) was submitted for your campaign.")
            ->line('Review and approve or request changes when ready.');
    }
}
