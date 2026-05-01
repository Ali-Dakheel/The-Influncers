<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Campaign $campaign): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($campaign->brand_id === $user->id) {
            return true;
        }

        return $campaign->isPublished() || $campaign->isPaused() || $campaign->isClosed();
    }

    public function create(User $user): bool
    {
        return $user->isBrand() || $user->isAdmin();
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return $user->isAdmin() || $campaign->brand_id === $user->id;
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        return $user->isAdmin() || ($campaign->brand_id === $user->id && $campaign->isDraft());
    }

    public function transitionState(User $user, Campaign $campaign): bool
    {
        return $user->isAdmin() || $campaign->brand_id === $user->id;
    }

    public function viewApplications(User $user, Campaign $campaign): bool
    {
        return $user->isAdmin() || $campaign->brand_id === $user->id;
    }
}
