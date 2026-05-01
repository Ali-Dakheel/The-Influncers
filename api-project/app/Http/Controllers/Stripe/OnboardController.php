<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Services\Stripe\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Stripe')]
class OnboardController extends Controller
{
    public function start(Request $request, StripeService $stripe): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->isInfluencer() || $user->isAgency(), 403);

        $result = $stripe->startOnboarding($user);

        return response()->json([
            'account_id' => $result['account_id'],
            'onboarding_url' => $result['onboarding_url'],
            'onboarded' => $user->fresh()->stripe_onboarded,
        ]);
    }

    /**
     * Stub: in production this is a webhook handler from Stripe.
     * Here it lets the influencer self-confirm onboarding for dev/demo.
     */
    public function complete(Request $request, StripeService $stripe): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->isInfluencer() || $user->isAgency(), 403);
        abort_if($user->stripe_account_id === null, 422, 'Onboarding has not been started.');

        $stripe->markOnboarded($user);

        return response()->json(['onboarded' => true]);
    }
}
