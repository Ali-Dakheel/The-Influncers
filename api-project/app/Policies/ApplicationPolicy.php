<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Application $application): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($application->influencer_id === $user->id) {
            return true;
        }

        return $application->campaign->brand_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isInfluencer() || $user->isAgency();
    }

    public function decide(User $user, Application $application): bool
    {
        return $user->isAdmin() || $application->campaign->brand_id === $user->id;
    }

    public function withdraw(User $user, Application $application): bool
    {
        return $user->isAdmin() || $application->influencer_id === $user->id;
    }

    public function submitDraft(User $user, Application $application): bool
    {
        return $user->isAdmin() || $application->influencer_id === $user->id;
    }
}
