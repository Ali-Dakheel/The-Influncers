<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $application = Application::factory()->accepted()->create();

        return [
            'campaign_id' => $application->campaign_id,
            'application_id' => $application->id,
            'brand_id' => $application->campaign->brand_id,
            'influencer_id' => $application->influencer_id,
            'amount_cents' => $application->proposed_price_cents ?? 100000,
            'currency' => 'USD',
            'status' => PaymentStatus::Pending,
            'stripe_payment_intent_id' => 'pi_test_'.fake()->lexify('????????????'),
        ];
    }

    public function escrowed(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::Escrowed,
            'escrowed_at' => now(),
        ]);
    }

    public function released(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::Released,
            'escrowed_at' => now()->subDay(),
            'released_at' => now(),
            'stripe_transfer_id' => 'tr_test_'.fake()->lexify('????????????'),
        ]);
    }
}
