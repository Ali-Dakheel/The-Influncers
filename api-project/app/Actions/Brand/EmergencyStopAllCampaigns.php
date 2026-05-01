<?php

namespace App\Actions\Brand;

use App\Enums\ApplicationStatus;
use App\Enums\CampaignState;
use App\Models\Campaign;
use App\Models\User;
use App\Notifications\CampaignEmergencyPausedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class EmergencyStopAllCampaigns
{
    /**
     * @return Collection<int, Campaign> The campaigns that were paused.
     */
    public function __invoke(User $brand, ?string $reason = null): Collection
    {
        return DB::transaction(function () use ($brand, $reason): Collection {
            $campaigns = Campaign::query()
                ->where('brand_id', $brand->id)
                ->where('state', CampaignState::Published)
                ->get();

            foreach ($campaigns as $campaign) {
                $campaign->update([
                    'state' => CampaignState::Paused,
                    'paused_at' => now(),
                ]);

                $influencers = $campaign->applications()
                    ->where('status', ApplicationStatus::Accepted)
                    ->with('influencer')
                    ->get()
                    ->pluck('influencer')
                    ->filter();

                Notification::send(
                    $influencers,
                    new CampaignEmergencyPausedNotification($campaign->fresh(), $reason)
                );
            }

            return $campaigns;
        });
    }
}
