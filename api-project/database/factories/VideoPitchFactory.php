<?php

namespace Database\Factories;

use App\Enums\VideoPitchStatus;
use App\Models\User;
use App\Models\VideoPitch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VideoPitch>
 */
class VideoPitchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'influencer_id' => User::factory(),
            'brand_id' => User::factory()->brand(),
            'campaign_id' => null,
            'video_url' => fake()->url(),
            'message' => fake()->paragraph(),
            'status' => VideoPitchStatus::Pending,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => VideoPitchStatus::Accepted,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => VideoPitchStatus::Rejected,
            'reviewed_at' => now(),
        ]);
    }
}
