<?php

namespace App\Listeners;

use App\Actions\Payment\GenerateInvoicesForPayment;
use App\Actions\Payment\ReleasePayment;
use App\Enums\PaymentStatus;
use App\Events\CampaignCompleted;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReleasePaymentsAndGenerateInvoices implements ShouldQueue
{
    public function __construct(
        private ReleasePayment $release,
        private GenerateInvoicesForPayment $generate,
    ) {}

    public function handle(CampaignCompleted $event): void
    {
        $payments = Payment::where('campaign_id', $event->campaign->id)
            ->where('status', PaymentStatus::Escrowed)
            ->get();

        foreach ($payments as $payment) {
            $released = ($this->release)($payment);

            if ($released->isReleased()) {
                ($this->generate)($released);
            }
        }
    }
}
