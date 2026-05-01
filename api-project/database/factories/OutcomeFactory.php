<?php

namespace Database\Factories;

use App\Enums\CampaignCategory;
use App\Enums\CampaignFormat;
use App\Enums\CampaignObjective;
use App\Enums\Platform;
use App\Models\Application;
use App\Models\Outcome;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Outcome>
 */
class OutcomeFactory extends Factory
{
    public function definition(): array
    {
        $application = Application::factory()->accepted()->create();

        return [
            'campaign_id' => $application->campaign_id,
            'application_id' => $application->id,
            'influencer_id' => $application->influencer_id,
            'platform' => Platform::Instagram,
            'category' => CampaignCategory::Fashion,
            'country_id' => null,
            'format' => CampaignFormat::Reel,
            'objective' => CampaignObjective::Engagement,
            'final_post_url' => null,
            'reach' => null,
            'engagement' => null,
            'conversions' => null,
            'cost_per_result_cents' => null,
            'paid_price_cents' => $application->proposed_price_cents,
            'recorded_at' => null,
        ];
    }

    public function recorded(): static
    {
        return $this->state(fn () => [
            'final_post_url' => fake()->url(),
            'reach' => fake()->numberBetween(1000, 1000000),
            'engagement' => fake()->numberBetween(50, 100000),
            'conversions' => fake()->numberBetween(0, 10000),
            'cost_per_result_cents' => fake()->numberBetween(10, 5000),
            'recorded_at' => now(),
        ]);
    }
}
