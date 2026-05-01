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
class SalesAttributionController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->isAdmin(), 403);

        $brandIds = User::where('sales_rep_id', $user->id)->pluck('id');

        $totalRevenue = Payment::query()
            ->whereIn('brand_id', $brandIds)
            ->whereIn('status', [PaymentStatus::Escrowed, PaymentStatus::Released])
            ->sum('amount_cents');

        $byBrand = Payment::query()
            ->whereIn('brand_id', $brandIds)
            ->whereIn('status', [PaymentStatus::Escrowed, PaymentStatus::Released])
            ->selectRaw('brand_id, SUM(amount_cents) as total')
            ->groupBy('brand_id')
            ->get()
            ->map(fn ($row) => [
                'brand_id' => (int) $row->brand_id,
                'total_cents' => (int) $row->total,
            ]);

        return response()->json([
            'data' => [
                'sales_rep_id' => $user->id,
                'brand_count' => $brandIds->count(),
                'total_revenue_cents' => (int) $totalRevenue,
                'by_brand' => $byBrand,
            ],
        ]);
    }

    public function assignSalesRep(Request $request, User $brand): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->isAdmin(), 403);
        abort_unless($brand->isBrand(), 422, 'Target user is not a brand.');

        $data = $request->validate([
            'sales_rep_id' => ['nullable', 'exists:users,id'],
        ]);

        $brand->update(['sales_rep_id' => $data['sales_rep_id']]);

        return response()->json([
            'data' => [
                'brand_id' => $brand->id,
                'sales_rep_id' => $brand->sales_rep_id,
            ],
        ]);
    }
}
