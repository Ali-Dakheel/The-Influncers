<?php

namespace App\Enums;

enum CampaignObjective: string
{
    case Awareness = 'awareness';
    case Engagement = 'engagement';
    case Conversions = 'conversions';
    case Sales = 'sales';
    case AppInstalls = 'app_installs';
    case Leads = 'leads';

    public static function values(): array
    {
        return array_map(fn (self $o) => $o->value, self::cases());
    }
}
