<?php

namespace App\Console\Commands;

use App\Actions\Campaign\CloseCampaign;
use App\Enums\CampaignState;
use App\Models\Campaign;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('campaigns:close-expired')]
#[Description('Auto-close campaigns whose ends_on date has passed and which are still in published or paused state.')]
class CloseExpiredCampaigns extends Command
{
    public function handle(CloseCampaign $closeCampaign): int
    {
        $expired = Campaign::query()
            ->whereIn('state', [CampaignState::Published, CampaignState::Paused])
            ->whereNotNull('ends_on')
            ->whereDate('ends_on', '<', now()->toDateString())
            ->get();

        foreach ($expired as $campaign) {
            $closeCampaign($campaign);
            $this->info("Closed campaign #{$campaign->id} — '{$campaign->title}'");
        }

        $this->info("Total closed: {$expired->count()}");

        return self::SUCCESS;
    }
}
