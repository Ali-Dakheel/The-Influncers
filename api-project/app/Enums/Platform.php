<?php

namespace App\Enums;

enum Platform: string
{
    case Instagram = 'instagram';
    case TikTok = 'tiktok';
    case YouTube = 'youtube';
    case X = 'x';
    case LinkedIn = 'linkedin';
    case Facebook = 'facebook';
    case Twitch = 'twitch';
    case Snapchat = 'snapchat';

    public static function values(): array
    {
        return array_map(fn (self $p) => $p->value, self::cases());
    }
}
