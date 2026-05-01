<?php

namespace Database\Factories;

use App\Models\PricePackage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PricePackage>
 */
class PricePackageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => '5 IG + 5 TikTok + 5 YouTube',
            'description' => 'Cross-platform launch bundle',
            'items' => [
                ['platform' => 'instagram', 'format' => 'reel', 'quantity' => 5],
                ['platform' => 'tiktok', 'format' => 'video', 'quantity' => 5],
                ['platform' => 'youtube', 'format' => 'video', 'quantity' => 5],
            ],
            'discount_pct' => 20,
            'total_cents' => 800000,
            'currency' => 'USD',
        ];
    }
}
