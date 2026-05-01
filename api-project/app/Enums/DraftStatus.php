<?php

namespace App\Enums;

enum DraftStatus: string
{
    case Submitted = 'submitted';
    case ChangesRequested = 'changes_requested';
    case Approved = 'approved';

    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
