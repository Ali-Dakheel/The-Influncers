<?php

namespace App\Http\Controllers\Brand;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Brand')]
class BudgetController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->isBrand() || $user->isAdmin(), 403);

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $monthSpend = Payment::query()
            ->where('brand_id', $user->id)
            ->whereIn('status', [PaymentStatus::Escrowed, PaymentStatus::Released])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount_cents');

        $lifetimeSpend = Payment::query()
            ->where('brand_id', $user->id)
            ->whereIn('status', [PaymentStatus::Escrowed, PaymentStatus::Released])
            ->sum('amount_cents');

        $monthlyBudget = $user->monthly_budget_cents ?? 0;

        return response()->json([
            'data' => [
                'monthly_budget_cents' => (int) $monthlyBudget,
                'month_spend_cents' => (int) $monthSpend,
                'month_remaining_cents' => $monthlyBudget ? max(0, (int) $monthlyBudget - (int) $monthSpend) : null,
                'month_pct_used' => $monthlyBudget ? round(($monthSpend / $monthlyBudget) * 100, 2) : null,
                'lifetime_spend_cents' => (int) $lifetimeSpend,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->isBrand() || $user->isAdmin(), 403);

        $data = $request->validate([
            'monthly_budget_cents' => ['nullable', 'integer', 'min:0'],
        ]);

        $user->update($data);

        return $this->show($request);
    }
}
