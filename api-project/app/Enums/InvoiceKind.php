<?php

namespace App\Enums;

enum InvoiceKind: string
{
    case BrandCharge = 'brand_charge';     // brand pays into platform
    case InfluencerPayout = 'influencer_payout'; // platform pays influencer

    public static function values(): array
    {
        return array_map(fn (self $k) => $k->value, self::cases());
    }
}
