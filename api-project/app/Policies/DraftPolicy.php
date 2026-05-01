<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\Draft;
use App\Models\User;

class DraftPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Draft $draft): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $application = $draft->application;

        return $application->influencer_id === $user->id
            || $application->campaign->brand_id === $user->id;
    }

    public function submit(User $user, Application $application): bool
    {
        return $user->isAdmin() || $application->influencer_id === $user->id;
    }

    public function review(User $user, Draft $draft): bool
    {
        return $user->isAdmin() || $draft->application->campaign->brand_id === $user->id;
    }
}
