<?php

namespace App\Listeners;

use App\Actions\Payment\CreatePaymentForApplication;
use App\Events\ApplicationAccepted;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreatePaymentIntentForAcceptedApplication implements ShouldQueue
{
    public function __construct(private CreatePaymentForApplication $action) {}

    public function handle(ApplicationAccepted $event): void
    {
        ($this->action)($event->application);
    }
}
