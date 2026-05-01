<?php

namespace App\Actions\Creator;

use App\Models\PriceListItem;
use App\Models\User;

class SetPriceListItem
{
    /**
     * Create or update a price for a (platform, format) combination.
     *
     * @param  array{platform: string, format: string, price_cents: int, currency?: string}  $attributes
     */
    public function __invoke(User $user, array $attributes): PriceListItem
    {
        $item = PriceListItem::updateOrCreate(
            [
                'user_id' => $user->id,
                'platform' => $attributes['platform'],
                'format' => $attributes['format'],
            ],
            [
                'price_cents' => $attributes['price_cents'],
                'currency' => $attributes['currency'] ?? 'USD',
            ]
        );

        return $item->fresh();
    }
}
