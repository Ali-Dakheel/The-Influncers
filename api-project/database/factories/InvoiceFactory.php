<?php

namespace Database\Factories;

use App\Enums\InvoiceKind;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $payment = Payment::factory()->released()->create();

        return [
            'number' => 'INV-'.strtoupper(fake()->unique()->bothify('????######')),
            'payment_id' => $payment->id,
            'recipient_id' => $payment->brand_id,
            'kind' => InvoiceKind::BrandCharge,
            'amount_cents' => $payment->amount_cents,
            'currency' => $payment->currency,
            'issued_at' => now(),
            'paid_at' => now(),
            'snapshot' => [],
        ];
    }

    public function influencerPayout(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'kind' => InvoiceKind::InfluencerPayout,
            ];
        });
    }
}
