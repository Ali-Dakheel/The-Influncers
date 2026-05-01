<?php

namespace App\Enums;

enum CampaignFormat: string
{
    case Post = 'post';
    case Story = 'story';
    case Reel = 'reel';
    case Video = 'video';
    case Livestream = 'livestream';
    case Article = 'article';

    public static function values(): array
    {
        return array_map(fn (self $f) => $f->value, self::cases());
    }
}
