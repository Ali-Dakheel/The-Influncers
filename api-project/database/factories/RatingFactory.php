<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    public function definition(): array
    {
        $application = Application::factory()->accepted()->create();

        return [
            'campaign_id' => $application->campaign_id,
            'application_id' => $application->id,
            'brand_id' => $application->campaign->brand_id,
            'influencer_id' => $application->influencer_id,
            'score' => fake()->numberBetween(3, 5),
            'text' => fake()->sentence(),
            'posted_at' => now(),
        ];
    }
}
