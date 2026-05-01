<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Payments')]
class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Payment::query()->latest();

        if ($user->isBrand()) {
            $query->where('brand_id', $user->id);
        } elseif ($user->isInfluencer() || $user->isAgency()) {
            $query->where('influencer_id', $user->id);
        } elseif (! $user->isAdmin()) {
            return PaymentResource::collection(collect());
        }

        return PaymentResource::collection($query->paginate());
    }

    public function show(Request $request, Payment $payment): PaymentResource
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
                || $payment->brand_id === $user->id
                || $payment->influencer_id === $user->id,
            403
        );

        return new PaymentResource($payment);
    }
}
