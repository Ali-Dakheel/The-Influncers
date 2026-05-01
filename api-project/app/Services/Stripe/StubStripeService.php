<?php

namespace App\Services\Stripe;

use App\Models\User;
use Illuminate\Support\Str;

/**
 * Returns deterministic-looking fake Stripe IDs so the rest of the app can be
 * tested and developed without configuring real Stripe API keys.
 *
 * Swap to a real implementation in StripeServiceProvider when keys are available.
 */
class StubStripeService implements StripeService
{
    public function startOnboarding(User $influencer): array
    {
        $accountId = $influencer->stripe_account_id ?? 'acct_'.Str::random(16);

        $influencer->stripe_account_id = $accountId;
        $influencer->save();

        return [
            'account_id' => $accountId,
            'onboarding_url' => "https://connect.stripe.com/setup/e/{$accountId}",
        ];
    }

    public function markOnboarded(User $influencer): void
    {
        $influencer->stripe_onboarded = true;
        $influencer->save();
    }

    public function createEscrowedPaymentIntent(int $amountCents, string $currency, User $brand): array
    {
        return ['payment_intent_id' => 'pi_'.Str::random(20)];
    }

    public function releaseTransfer(string $paymentIntentId, int $amountCents, string $currency, User $influencer): array
    {
        return ['transfer_id' => 'tr_'.Str::random(20)];
    }

    public function refund(string $paymentIntentId): void
    {
        // no-op for the stub
    }
}
