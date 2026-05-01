<?php

namespace App\Observers;

use App\Events\CampaignDatesChanged;
use App\Models\Campaign;

class CampaignObserver
{
    private const DATE_FIELDS = ['starts_on', 'ends_on', 'application_deadline'];

    public function updated(Campaign $campaign): void
    {
        $changes = [];

        foreach (self::DATE_FIELDS as $field) {
            if ($campaign->wasChanged($field)) {
                $changes[$field] = [
                    'old' => $campaign->getOriginal($field),
                    'new' => $campaign->getAttribute($field),
                ];
            }
        }

        if ($changes) {
            event(new CampaignDatesChanged($campaign, $changes));
        }
    }
}
