<?php

namespace App\Listeners;

use App\Enums\ApplicationStatus;
use App\Events\CampaignDatesChanged;
use App\Notifications\CampaignDateChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyOfCampaignDateChange implements ShouldQueue
{
    public function handle(CampaignDatesChanged $event): void
    {
        $influencers = $event->campaign->applications()
            ->where('status', ApplicationStatus::Accepted)
            ->with('influencer')
            ->get()
            ->pluck('influencer')
            ->filter();

        Notification::send(
            $influencers,
            new CampaignDateChangedNotification($event->campaign, $event->changedDates)
        );
    }
}
