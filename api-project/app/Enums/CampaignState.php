<?php

namespace App\Enums;

enum CampaignState: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Paused = 'paused';
    case Closed = 'closed';
    case Completed = 'completed';

    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
