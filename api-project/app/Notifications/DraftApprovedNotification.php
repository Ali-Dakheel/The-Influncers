<?php

namespace App\Notifications;

use App\Models\Draft;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DraftApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Draft approved')
            ->line("Your draft (revision #{$this->draft->revision_number}) was approved. Time to post.")
            ->lineIf((bool) $this->draft->review_note, "Note from brand: {$this->draft->review_note}");
    }
}
