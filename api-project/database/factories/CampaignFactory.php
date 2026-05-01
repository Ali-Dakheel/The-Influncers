<?php

namespace Database\Factories;

use App\Enums\CampaignCategory;
use App\Enums\CampaignFormat;
use App\Enums\CampaignObjective;
use App\Enums\CampaignState;
use App\Enums\Platform;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'brand_id' => User::factory()->brand(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'deliverables' => fake()->sentence(),
            'category' => fake()->randomElement(CampaignCategory::cases()),
            'country_id' => null,
            'platforms' => fake()->randomElements(Platform::values(), 2),
            'format' => fake()->randomElement(CampaignFormat::cases()),
            'objective' => fake()->randomElement(CampaignObjective::cases()),
            'budget_cents' => fake()->numberBetween(50000, 5000000),
            'currency' => 'USD',
            'state' => CampaignState::Draft,
            'starts_on' => fake()->dateTimeBetween('+1 week', '+2 weeks'),
            'ends_on' => fake()->dateTimeBetween('+3 weeks', '+6 weeks'),
            'application_deadline' => fake()->dateTimeBetween('+3 days', '+10 days'),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'state' => CampaignState::Published,
            'published_at' => now(),
        ]);
    }

    public function paused(): static
    {
        return $this->state(fn () => [
            'state' => CampaignState::Paused,
            'published_at' => now()->subDay(),
            'paused_at' => now(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'state' => CampaignState::Closed,
            'published_at' => now()->subWeek(),
            'closed_at' => now(),
        ]);
    }
}
