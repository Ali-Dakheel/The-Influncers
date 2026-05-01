<?php

namespace App\Enums;

enum VideoPitchStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
