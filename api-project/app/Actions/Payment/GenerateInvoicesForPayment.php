<?php

namespace App\Actions\Payment;

use App\Enums\InvoiceKind;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateInvoicesForPayment
{
    /**
     * Issues two invoices when a payment is released:
     *  - brand_charge:      brand   ←  charged for the campaign
     *  - influencer_payout: creator ←  paid for the deliverable
     *
     * @return array{0: Invoice, 1: Invoice}
     */
    public function __invoke(Payment $payment): array
    {
        return DB::transaction(function () use ($payment): array {
            $snapshot = [
                'campaign' => [
                    'id' => $payment->campaign_id,
                    'title' => $payment->campaign->title,
                ],
                'brand' => [
                    'id' => $payment->brand_id,
                    'name' => $payment->brand->name,
                    'email' => $payment->brand->email,
                ],
                'influencer' => [
                    'id' => $payment->influencer_id,
                    'name' => $payment->influencer->name,
                    'email' => $payment->influencer->email,
                ],
            ];

            $brandInvoice = Invoice::firstOrCreate(
                ['payment_id' => $payment->id, 'kind' => InvoiceKind::BrandCharge],
                [
                    'number' => 'INV-B-'.Str::upper(Str::random(10)),
                    'recipient_id' => $payment->brand_id,
                    'amount_cents' => $payment->amount_cents,
                    'currency' => $payment->currency,
                    'issued_at' => now(),
                    'paid_at' => now(),
                    'snapshot' => $snapshot,
                ]
            );

            $payoutInvoice = Invoice::firstOrCreate(
                ['payment_id' => $payment->id, 'kind' => InvoiceKind::InfluencerPayout],
                [
                    'number' => 'INV-P-'.Str::upper(Str::random(10)),
                    'recipient_id' => $payment->influencer_id,
                    'amount_cents' => $payment->amount_cents,
                    'currency' => $payment->currency,
                    'issued_at' => now(),
                    'paid_at' => now(),
                    'snapshot' => $snapshot,
                ]
            );

            return [$brandInvoice, $payoutInvoice];
        });
    }
}
