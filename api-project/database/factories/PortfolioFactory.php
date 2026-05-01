<?php

namespace Database\Factories;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Portfolio>
 */
class PortfolioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => fake()->paragraph(),
            'content_style' => fake()->randomElements(['lifestyle', 'beauty', 'fitness', 'fashion', 'tech', 'gaming'], 3),
            'audience_size' => fake()->numberBetween(1000, 1000000),
            'audience_demographics' => [
                'age_buckets' => ['18-24' => 30, '25-34' => 45, '35-44' => 25],
                'gender_split' => ['female' => 60, 'male' => 38, 'other' => 2],
            ],
            'past_collabs' => [],
        ];
    }
}
