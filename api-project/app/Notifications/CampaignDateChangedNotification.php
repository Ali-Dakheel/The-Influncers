<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignDateChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, array{old: mixed, new: mixed}>  $changedDates
     */
    public function __construct(public Campaign $campaign, public array $changedDates) {}

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
            ->subject(__('messages.campaign.date_changed').' — '.$this->campaign->title)
            ->line("Dates were updated on '{$this->campaign->title}':");

        foreach ($this->changedDates as $field => $diff) {
            $old = is_object($diff['old']) ? (string) $diff['old'] : ($diff['old'] ?? 'null');
            $new = is_object($diff['new']) ? (string) $diff['new'] : ($diff['new'] ?? 'null');
            $msg->line("- {$field}: {$old} → {$new}");
        }

        return $msg->line('No action required — just a heads up.');
    }
}
