<?php

namespace App\Console\Commands;

use App\Enums\CampaignState;
use App\Models\Campaign;
use App\Models\Draft;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('content:archive {--days=90 : Days since completion before archiving}')]
#[Description('Archive completed/closed campaigns and their drafts after the configured retention window.')]
class ArchiveOldCampaigns extends Command
{
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $campaigns = Campaign::query()
            ->whereNull('archived_at')
            ->whereIn('state', [CampaignState::Completed, CampaignState::Closed])
            ->where(function ($q) use ($cutoff) {
                $q->where('completed_at', '<=', $cutoff)
                    ->orWhere('closed_at', '<=', $cutoff);
            })
            ->get();

        foreach ($campaigns as $campaign) {
            $campaign->update(['archived_at' => now()]);

            Draft::query()
                ->whereIn('application_id', $campaign->applications()->pluck('id'))
                ->whereNull('archived_at')
                ->update(['archived_at' => now()]);

            $this->info("Archived campaign #{$campaign->id} — '{$campaign->title}'");
        }

        $this->info("Total archived: {$campaigns->count()}");

        return self::SUCCESS;
    }
}
