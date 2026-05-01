<?php

namespace App\Actions\Payment;

use App\Enums\PaymentStatus;
use App\Models\Application;
use App\Models\Payment;
use App\Services\Stripe\StripeService;

class CreatePaymentForApplication
{
    public function __construct(private StripeService $stripe) {}

    public function __invoke(Application $application): ?Payment
    {
        if (! $application->proposed_price_cents) {
            return null;
        }

        if (Payment::where('application_id', $application->id)->exists()) {
            return null; // already created
        }

        $brand = $application->campaign->brand;

        $intent = $this->stripe->createEscrowedPaymentIntent(
            $application->proposed_price_cents,
            $application->currency,
            $brand,
        );

        return Payment::create([
            'campaign_id' => $application->campaign_id,
            'application_id' => $application->id,
            'brand_id' => $brand->id,
            'influencer_id' => $application->influencer_id,
            'amount_cents' => $application->proposed_price_cents,
            'currency' => $application->currency,
            'status' => PaymentStatus::Escrowed,
            'stripe_payment_intent_id' => $intent['payment_intent_id'],
            'escrowed_at' => now(),
        ]);
    }
}
