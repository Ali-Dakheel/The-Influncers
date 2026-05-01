<?php

namespace Database\Factories;

use App\Enums\DraftStatus;
use App\Enums\Platform;
use App\Models\Application;
use App\Models\Draft;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Draft>
 */
class DraftFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory()->accepted(),
            'revision_number' => 1,
            'platform' => Platform::Instagram,
            'file_url' => fake()->imageUrl(),
            'caption' => fake()->sentence(),
            'status' => DraftStatus::Submitted,
            'submitted_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => DraftStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }

    public function changesRequested(): static
    {
        return $this->state(fn () => [
            'status' => DraftStatus::ChangesRequested,
            'reviewed_at' => now(),
        ]);
    }
}
