<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

#[Group('Payments')]
class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Invoice::query()->latest('issued_at');

        if (! $user->isAdmin()) {
            $query->where('recipient_id', $user->id);
        }

        return InvoiceResource::collection($query->paginate());
    }

    public function show(Request $request, Invoice $invoice): InvoiceResource
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || $invoice->recipient_id === $user->id,
            403
        );

        return new InvoiceResource($invoice);
    }
}
