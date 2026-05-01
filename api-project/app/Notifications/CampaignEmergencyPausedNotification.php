<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignEmergencyPausedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Campaign $campaign, public ?string $reason = null) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $msg = (new MailMessage)
            ->subject("URGENT: Campaign '{$this->campaign->title}' has been paused")
            ->line('The brand has activated emergency pause on this campaign. All work should stop immediately.');

        if ($this->reason) {
            $msg->line("Reason: {$this->reason}");
        }

        return $msg->line('You will be notified when the campaign resumes or is officially closed.');
    }
}
