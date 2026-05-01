<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory()->published(),
            'influencer_id' => User::factory(),
            'status' => ApplicationStatus::Pending,
            'pitch' => fake()->paragraph(),
            'proposed_price_cents' => fake()->numberBetween(10000, 1000000),
            'currency' => 'USD',
            'applied_at' => now(),
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => ApplicationStatus::Accepted,
            'decided_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => ApplicationStatus::Rejected,
            'decided_at' => now(),
        ]);
    }

    public function withdrawn(): static
    {
        return $this->state(fn () => [
            'status' => ApplicationStatus::Withdrawn,
        ]);
    }
}
