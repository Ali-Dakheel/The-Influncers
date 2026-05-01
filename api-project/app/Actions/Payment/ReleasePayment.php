<?php

namespace App\Actions\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\Stripe\StripeService;

class ReleasePayment
{
    public function __construct(private StripeService $stripe) {}

    public function __invoke(Payment $payment): Payment
    {
        if ($payment->status !== PaymentStatus::Escrowed) {
            return $payment;
        }

        $transfer = $this->stripe->releaseTransfer(
            $payment->stripe_payment_intent_id,
            $payment->amount_cents,
            $payment->currency,
            $payment->influencer,
        );

        $payment->update([
            'status' => PaymentStatus::Released,
            'released_at' => now(),
            'stripe_transfer_id' => $transfer['transfer_id'],
        ]);

        return $payment->fresh();
    }
}
