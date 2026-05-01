<?php

namespace App\Listeners;

use App\Enums\ApplicationStatus;
use App\Events\CampaignCompleted;
use App\Notifications\CampaignCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendCampaignCompletedNotification implements ShouldQueue
{
    public function handle(CampaignCompleted $event): void
    {
        $influencers = $event->campaign->applications()
            ->where('status', ApplicationStatus::Accepted)
            ->with('influencer')
            ->get()
            ->pluck('influencer')
            ->filter();

        Notification::send($influencers, new CampaignCompletedNotification($event->campaign));
    }
}
