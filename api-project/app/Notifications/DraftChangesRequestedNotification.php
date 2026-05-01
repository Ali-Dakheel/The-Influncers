<?php

namespace App\Notifications;

use App\Models\Draft;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DraftChangesRequestedNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Changes requested on your draft')
            ->line("The brand requested changes to your draft (revision #{$this->draft->revision_number}).")
            ->line("Note: {$this->draft->review_note}")
            ->line('Submit a new revision when ready.');
    }
}
