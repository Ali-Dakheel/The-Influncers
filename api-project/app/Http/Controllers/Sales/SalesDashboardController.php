<?php

namespace App\Http\Controllers\Sales;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Sales')]
class SalesDashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->isAdmin(), 403);

        $brands = User::query()
            ->where('sales_rep_id', $user->id)
            ->get();

        $brandIds = $brands->pluck('id');

        // Top 5 accounts by lifetime spend
        $topAccounts = Payment::query()
            ->whereIn('brand_id', $brandIds)
            ->whereIn('status', [PaymentStatus::Escrowed, PaymentStatus::Released])
            ->selectRaw('brand_id, SUM(amount_cents) as total, COUNT(*) as count')
            ->groupBy('brand_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'brand_id' => (int) $row->brand_id,
                'brand_name' => User::find($row->brand_id)->name,
                'total_spend_cents' => (int) $row->total,
                'campaign_count' => (int) $row->count,
            ]);

        // Churn signal: brands with no payments in last 60 days
        $cutoff = now()->subDays(60);
        $activeBrandIds = Payment::query()
            ->whereIn('brand_id', $brandIds)
            ->where('created_at', '>=', $cutoff)
            ->distinct('brand_id')
            ->pluck('brand_id');

        $churnRisk = $brands
            ->whereNotIn('id', $activeBrandIds)
            ->map(fn (User $brand) => [
                'brand_id' => $brand->id,
                'brand_name' => $brand->name,
                'last_payment_at' => Payment::where('brand_id', $brand->id)
                    ->latest()
                    ->value('created_at')?->toIso8601String(),
            ])
            ->values();

        // This-month new revenue
        $monthStart = now()->startOfMonth();
        $thisMonthRevenue = Payment::query()
            ->whereIn('brand_id', $brandIds)
            ->whereIn('status', [PaymentStatus::Escrowed, PaymentStatus::Released])
            ->where('created_at', '>=', $monthStart)
            ->sum('amount_cents');

        return response()->json([
            'data' => [
                'sales_rep_id' => $user->id,
                'brand_count' => $brands->count(),
                'this_month_revenue_cents' => (int) $thisMonthRevenue,
                'top_accounts' => $topAccounts,
                'churn_risk_count' => $churnRisk->count(),
                'churn_risk' => $churnRisk,
            ],
        ]);
    }
}
