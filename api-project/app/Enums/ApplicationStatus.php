<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';

    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
