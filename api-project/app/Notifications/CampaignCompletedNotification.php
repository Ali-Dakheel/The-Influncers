<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Campaign $campaign) {}

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
            ->subject("Campaign completed — '{$this->campaign->title}'")
            ->line('The campaign has been marked as completed.')
            ->line('Record your final post URL and metrics so they appear in your performance history.');
    }
}
