<?php

namespace App\Services\Stripe;

use App\Models\User;

/**
 * Thin wrapper around Stripe Connect operations.
 *
 * In production, this calls the Stripe SDK. The default binding here is the
 * StubStripeService which returns deterministic fake IDs so tests can run
 * and the rest of the app is wired correctly. Replace the binding in a
 * real ServiceProvider once Stripe API keys are configured.
 */
interface StripeService
{
    /**
     * Begin Express account onboarding for an influencer.
     *
     * @return array{account_id: string, onboarding_url: string}
     */
    public function startOnboarding(User $influencer): array;

    /**
     * Mark an influencer's Stripe account as fully onboarded (webhook would do this in prod).
     */
    public function markOnboarded(User $influencer): void;

    /**
     * Create a held payment intent (escrow) for a brand → platform charge.
     *
     * @return array{payment_intent_id: string}
     */
    public function createEscrowedPaymentIntent(int $amountCents, string $currency, User $brand): array;

    /**
     * Release escrowed funds to the influencer's connected account.
     *
     * @return array{transfer_id: string}
     */
    public function releaseTransfer(string $paymentIntentId, int $amountCents, string $currency, User $influencer): array;

    /**
     * Refund a held payment intent.
     */
    public function refund(string $paymentIntentId): void;
}
