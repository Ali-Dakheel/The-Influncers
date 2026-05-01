<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DraftDeadlineReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $campaign = $this->application->campaign;
        $deadline = $campaign->application_deadline?->toDayDateTimeString() ?? 'soon';

        return (new MailMessage)
            ->subject("Deadline reminder — '{$campaign->title}'")
            ->line("Your draft for '{$campaign->title}' is due {$deadline}.")
            ->line("If you've already submitted, you can ignore this. Otherwise, head to the app to upload.");
    }
}
