<?php

namespace App\Enums;

enum Role: string
{
    case Brand = 'brand';
    case Influencer = 'influencer';
    case Agency = 'agency';
    case Admin = 'admin';

    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }
}
