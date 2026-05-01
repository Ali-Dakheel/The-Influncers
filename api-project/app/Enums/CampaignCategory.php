<?php

namespace App\Enums;

enum CampaignCategory: string
{
    case Fashion = 'fashion';
    case Beauty = 'beauty';
    case Tech = 'tech';
    case Fitness = 'fitness';
    case Food = 'food';
    case Travel = 'travel';
    case Gaming = 'gaming';
    case Lifestyle = 'lifestyle';
    case Finance = 'finance';
    case Other = 'other';

    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }
}
