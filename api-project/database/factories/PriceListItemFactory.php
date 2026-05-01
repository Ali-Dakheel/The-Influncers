<?php

namespace Database\Factories;

use App\Enums\CampaignFormat;
use App\Enums\Platform;
use App\Models\PriceListItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PriceListItem>
 */
class PriceListItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'platform' => fake()->randomElement(Platform::cases()),
            'format' => fake()->randomElement(CampaignFormat::cases()),
            'price_cents' => fake()->numberBetween(10000, 1000000),
            'currency' => 'USD',
        ];
    }
}
