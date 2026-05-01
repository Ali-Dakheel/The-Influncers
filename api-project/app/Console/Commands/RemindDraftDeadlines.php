<?php

namespace App\Console\Commands;

use App\Enums\ApplicationStatus;
use App\Enums\CampaignState;
use App\Models\Application;
use App\Notifications\DraftDeadlineReminderNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('campaigns:remind-deadlines')]
#[Description('Send a reminder to influencers whose campaigns have an application/draft deadline in the next 24 hours and no submitted draft yet.')]
class RemindDraftDeadlines extends Command
{
    public function handle(): int
    {
        $now = now();
        $cutoff = $now->copy()->addDay();

        $applications = Application::query()
            ->where('status', ApplicationStatus::Accepted)
            ->whereDoesntHave('drafts')
            ->whereHas('campaign', function ($q) use ($now, $cutoff) {
                $q->where('state', CampaignState::Published)
                    ->whereNotNull('application_deadline')
                    ->whereBetween('application_deadline', [$now, $cutoff]);
            })
            ->with('influencer', 'campaign')
            ->get();

        foreach ($applications as $application) {
            if ($application->influencer) {
                $application->influencer->notify(
                    new DraftDeadlineReminderNotification($application)
                );
            }
        }

        $this->info("Reminders dispatched: {$applications->count()}");

        return self::SUCCESS;
    }
}
